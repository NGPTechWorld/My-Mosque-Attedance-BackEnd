<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * نقاط تلقائية حسب إعدادات الأدمن:
 * - حضور في الوقت  → إضافة نقاط الحضور.
 * - حضور بعد وقت التأخير الخاص بالفترة → خصم نقاط التأخير.
 * - تسجيل غياب → خصم نقاط الغياب.
 */
class AttendanceRewardService
{
    // الحضور
    public const KEY_ENABLED = 'attendance_reward_enabled';
    public const KEY_POINTS = 'attendance_reward_points';
    public const KEY_MESSAGE = 'attendance_reward_message';

    // التأخير
    public const KEY_LATE_ENABLED = 'late_penalty_enabled';
    public const KEY_LATE_POINTS = 'late_penalty_points';
    public const KEY_LATE_MESSAGE = 'late_penalty_message';

    // الغياب
    public const KEY_ABSENCE_ENABLED = 'absence_penalty_enabled';
    public const KEY_ABSENCE_POINTS = 'absence_penalty_points';
    public const KEY_ABSENCE_MESSAGE = 'absence_penalty_message';

    /**
     * يُطبَّق عند تسجيل حضور الطالب: خصم تأخير إن تأخّر، وإلا منح نقاط الحضور.
     */
    public function applyCheckIn(Student $student, Carbon $time): void
    {
        try {
            if ($this->isLate($student, $time) && Setting::get(self::KEY_LATE_ENABLED, '0') === '1') {
                $this->deduct($student, self::KEY_LATE_POINTS, self::KEY_LATE_MESSAGE, 'خصم تأخير');
                return;
            }

            // حضور في الوقت (أو التأخير غير مفعّل) → نقاط حضور
            if (Setting::get(self::KEY_ENABLED, '0') === '1') {
                $points = (int) Setting::get(self::KEY_POINTS, 0);
                if ($points > 0) {
                    $student->addPoints($points, $this->messageOr(self::KEY_MESSAGE, 'نقاط حضور'));
                }
            }
        } catch (\Throwable $e) {
            Log::error('Failed to apply check-in points: ' . $e->getMessage());
        }
    }

    /**
     * يُطبَّق عند تسجيل غياب الطالب: خصم نقاط الغياب إن كانت مفعّلة.
     */
    public function applyAbsence(Student $student): void
    {
        try {
            if (Setting::get(self::KEY_ABSENCE_ENABLED, '0') === '1') {
                $this->deduct($student, self::KEY_ABSENCE_POINTS, self::KEY_ABSENCE_MESSAGE, 'خصم غياب');
            }
        } catch (\Throwable $e) {
            Log::error('Failed to apply absence penalty: ' . $e->getMessage());
        }
    }

    /** هل وصل الطالب متأخراً عن وقت التأخير المحدّد لفترته؟ */
    private function isLate(Student $student, Carbon $time): bool
    {
        $lateTime = $student->shift?->late_time;
        if (! $lateTime) {
            return false; // لا يوجد وقت تأخير محدّد للفترة
        }
        return $time->format('H:i:s') > $this->normalizeTime($lateTime);
    }

    /** خصم نقاط (مع منع الرصيد السالب). */
    private function deduct(Student $student, string $pointsKey, string $messageKey, string $defaultMessage): void
    {
        $points = (int) Setting::get($pointsKey, 0);
        if ($points <= 0) {
            return;
        }

        $deduct = min($points, max((int) $student->points, 0));
        if ($deduct <= 0) {
            return; // لا رصيد للخصم
        }

        $student->addPoints(-$deduct, $this->messageOr($messageKey, $defaultMessage));
    }

    private function messageOr(string $key, string $default): string
    {
        $message = Setting::get($key);
        return ($message !== null && trim($message) !== '') ? $message : $default;
    }

    /** توحيد صيغة الوقت إلى H:i:s للمقارنة النصية. */
    private function normalizeTime(string $time): string
    {
        return strlen($time) === 5 ? $time . ':00' : $time;
    }
}
