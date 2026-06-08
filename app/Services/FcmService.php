<?php

namespace App\Services;

use App\Models\DeviceToken;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * خدمة إرسال إشعارات Firebase Cloud Messaging باستخدام HTTP v1 API.
 *
 * هذه الخدمة لا تعتمد على أي مكتبة خارجية: تقوم بتوقيع JWT يدوياً
 * باستخدام مفتاح الـ service account ثم تستبدله بـ access token من Google.
 */
class FcmService
{
    /**
     * إرسال إشعار لجميع أجهزة الأهل المرتبطة برقم هاتف معيّن.
     *
     * @param  string  $guardianPhone  رقم هاتف ولي الأمر
     * @param  string  $title          عنوان الإشعار
     * @param  string  $body           نص الإشعار
     * @param  array   $data           بيانات إضافية (تُرسل كـ data payload)
     * @return void
     */
    public function sendToGuardian(string $guardianPhone, string $title, string $body, array $data = []): void
    {
        $tokens = DeviceToken::where('guardian_phone', $guardianPhone)->pluck('token')->all();

        if (empty($tokens)) {
            return; // لا يوجد جهاز مسجّل لهذا الأهل
        }

        foreach ($tokens as $token) {
            $this->sendToToken($token, $title, $body, $data);
        }
    }

    /**
     * إرسال إشعار لجهاز واحد عبر FCM token.
     */
    public function sendToToken(string $token, string $title, string $body, array $data = []): void
    {
        $projectId = config('services.firebase.project_id');
        $accessToken = $this->getAccessToken();

        if (! $projectId || ! $accessToken) {
            Log::warning('FCM not configured (missing project_id or credentials). Skipping push.');
            return;
        }

        // كل قيم data في FCM يجب أن تكون نصوصاً
        $stringData = [];
        foreach ($data as $key => $value) {
            $stringData[$key] = (string) $value;
        }

        try {
            $response = Http::withToken($accessToken)
                ->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", [
                    'message' => [
                        'token' => $token,
                        'notification' => [
                            'title' => $title,
                            'body' => $body,
                        ],
                        'data' => $stringData,
                        'android' => [
                            'priority' => 'high',
                            'notification' => [
                                'sound' => 'default',
                                'channel_id' => 'attendance_channel',
                            ],
                        ],
                        'apns' => [
                            'payload' => [
                                'aps' => [
                                    'sound' => 'default',
                                ],
                            ],
                        ],
                    ],
                ]);

            if ($response->failed()) {
                // إذا كان التوكن غير صالح (UNREGISTERED) نحذفه من قاعدة البيانات
                $status = $response->json('error.status');
                if (in_array($status, ['UNREGISTERED', 'INVALID_ARGUMENT', 'NOT_FOUND'])) {
                    DeviceToken::where('token', $token)->delete();
                }
                Log::warning('FCM send failed: ' . $response->body());
            }
        } catch (\Throwable $e) {
            Log::error('FCM send exception: ' . $e->getMessage());
        }
    }

    /**
     * الحصول على access token من Google (مع تخزينه مؤقتاً ~55 دقيقة).
     */
    protected function getAccessToken(): ?string
    {
        return Cache::remember('fcm_access_token', now()->addMinutes(55), function () {
            $credentials = $this->loadCredentials();
            if (! $credentials) {
                return null;
            }

            $now = time();
            $jwt = $this->createSignedJwt($credentials, $now);
            if (! $jwt) {
                return null;
            }

            try {
                $response = Http::asForm()->post($credentials['token_uri'], [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion' => $jwt,
                ]);

                if ($response->failed()) {
                    Log::error('FCM token exchange failed: ' . $response->body());
                    return null;
                }

                return $response->json('access_token');
            } catch (\Throwable $e) {
                Log::error('FCM token exchange exception: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * قراءة ملف الـ service account.
     */
    protected function loadCredentials(): ?array
    {
        $path = config('services.firebase.credentials');

        if (! $path || ! is_file($path)) {
            return null;
        }

        $json = json_decode(file_get_contents($path), true);

        if (! isset($json['private_key'], $json['client_email'], $json['token_uri'])) {
            return null;
        }

        return $json;
    }

    /**
     * توقيع JWT بـ RS256 باستخدام مفتاح الـ service account.
     */
    protected function createSignedJwt(array $credentials, int $now): ?string
    {
        $header = ['alg' => 'RS256', 'typ' => 'JWT'];
        $claims = [
            'iss' => $credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => $credentials['token_uri'],
            'iat' => $now,
            'exp' => $now + 3600,
        ];

        $segments = [
            $this->base64UrlEncode(json_encode($header)),
            $this->base64UrlEncode(json_encode($claims)),
        ];

        $signingInput = implode('.', $segments);
        $signature = '';

        $success = openssl_sign($signingInput, $signature, $credentials['private_key'], OPENSSL_ALGO_SHA256);
        if (! $success) {
            Log::error('FCM JWT signing failed.');
            return null;
        }

        $segments[] = $this->base64UrlEncode($signature);

        return implode('.', $segments);
    }

    protected function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
