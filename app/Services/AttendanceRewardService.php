<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\Student;
use Illuminate\Support\Facades\Log;

/**
 * منح نقاط تلقائياً عند تسجيل حضور الطالب، حسب الإعدادات التي يحدّدها الأدمن
 * (تفعيل/إيقاف + عدد النقاط + رسالة سجل النقاط).
 */
class AttendanceRewardService
{
    public const KEY_ENABLED = 'attendance_reward_enabled';
    public const KEY_POINTS = 'attendance_reward_points';
    public const KEY_MESSAGE = 'attendance_reward_message';

    /**
     * يمنح الطالب نقاط الحضور إن كانت الميزة مفعّلة. آمن: لا يرمي استثناء.
     */
    public function award(Student $student): void
    {
        try {
            if (Setting::get(self::KEY_ENABLED, '0') !== '1') {
                return; // الميزة غير مفعّلة
            }

            $points = (int) Setting::get(self::KEY_POINTS, 0);
            if ($points <= 0) {
                return;
            }

            $message = Setting::get(self::KEY_MESSAGE);
            $message = ($message !== null && trim($message) !== '') ? $message : 'نقاط حضور';

            $student->addPoints($points, $message);
        } catch (\Throwable $e) {
            Log::error('Failed to award attendance points: ' . $e->getMessage());
        }
    }
}
