<?php

namespace App\Services;

use App\Models\ParentNotification;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * يبني إشعار الحضور (اسم اليوم + الوقت بالعربي)، يخزّنه في السجل،
 * ويرسله عبر Firebase. تستخدمه كل من واجهة الـ API ولوحة الويب.
 */
class AttendanceNotifier
{
    public function __construct(private FcmService $fcm)
    {
    }

    private const DAYS_AR = [
        'الأحد',
        'الإثنين',
        'الثلاثاء',
        'الأربعاء',
        'الخميس',
        'الجمعة',
        'السبت',
    ];

    public function notify(Student $student, Carbon $time): void
    {
        // اسم اليوم بالعربي (dayOfWeek: 0=الأحد ... 6=السبت)
        $dayName = self::DAYS_AR[$time->dayOfWeek] ?? '';

        // الوقت بصيغة 12 ساعة بالعربي (مثال: "3:05 مساءً")
        $timeFormatted = $time->format('g:i') . ' ' . ($time->format('A') === 'AM' ? 'صباحاً' : 'مساءً');

        $title = 'تسجيل حضور';
        $body = "تم تسجيل دخول الطالب {$student->name} يوم {$dayName} الساعة {$timeFormatted}";

        $data = [
            'type' => 'attendance',
            'student_id' => $student->id,
            'date' => $time->toDateString(),
            'day' => $dayName,
            'time' => $timeFormatted,
        ];

        // 1) حفظ الإشعار في السجل (يظهر داخل التطبيق)
        try {
            ParentNotification::create([
                'student_id' => $student->id,
                'guardian_phone' => $student->guardian_phone,
                'type' => 'attendance',
                'title' => $title,
                'body' => $body,
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to store attendance notification: ' . $e->getMessage());
        }

        // 2) إرسال إشعار Firebase لأجهزة الأهل
        try {
            $this->fcm->sendToGuardian($student->guardian_phone, $title, $body, $data);
        } catch (\Throwable $e) {
            Log::error('Failed to send FCM attendance notification: ' . $e->getMessage());
        }
    }

    /**
     * إشعار غياب الطالب: يُحفظ في السجل ويُرسل عبر Firebase.
     *
     * @param  string|null  $absenceType  excused | unexcused | null
     */
    public function notifyAbsence(Student $student, Carbon $date, ?string $absenceType = null): void
    {
        $dayName = self::DAYS_AR[$date->dayOfWeek] ?? '';

        $typeLabel = match ($absenceType) {
            'excused' => ' (غياب مبرّر)',
            'unexcused' => ' (غياب غير مبرّر)',
            default => '',
        };

        $title = 'تسجيل غياب';
        $body = "نفيدكم بغياب الطالب {$student->name} يوم {$dayName} بتاريخ {$date->toDateString()}{$typeLabel}";

        $data = [
            'type' => 'absence',
            'absence_type' => $absenceType ?? '',
            'student_id' => $student->id,
            'date' => $date->toDateString(),
            'day' => $dayName,
        ];

        // 1) حفظ الإشعار في السجل (يظهر داخل التطبيق)
        try {
            ParentNotification::create([
                'student_id' => $student->id,
                'guardian_phone' => $student->guardian_phone,
                'type' => 'absence',
                'title' => $title,
                'body' => $body,
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to store absence notification: ' . $e->getMessage());
        }

        // 2) إرسال إشعار Firebase لأجهزة الأهل
        try {
            $this->fcm->sendToGuardian($student->guardian_phone, $title, $body, $data);
        } catch (\Throwable $e) {
            Log::error('Failed to send FCM absence notification: ' . $e->getMessage());
        }
    }
}
