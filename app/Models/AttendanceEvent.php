<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * مناسبة حضور خاصة متكررة أسبوعياً (مثل «مجلس الصلاة على النبي»).
 */
class AttendanceEvent extends Model
{
    protected $fillable = [
        'name',
        'days',        // مصفوفة أيام الأسبوع (0=الأحد..6=السبت)
        'start_time',
        'end_time',
        'points',
        'message',
        'active',
    ];

    protected $casts = [
        'days' => 'array',
        'active' => 'boolean',
        'points' => 'integer',
    ];

    // الفترات المسموح لها التسجيل في هذه المناسبة
    public function shifts()
    {
        return $this->belongsToMany(Shift::class, 'attendance_event_shift')->withTimestamps();
    }

    public function attendances()
    {
        return $this->hasMany(EventAttendance::class);
    }

    /**
     * هل المناسبة فعّالة الآن لطالب في الفترة المحدّدة؟
     * (اليوم ضمن أيامها + الوقت ضمن النافذة + فترة الطالب مسموحة)
     */
    public function matchesNow(Carbon $now, ?int $shiftId): bool
    {
        if (! $this->active) {
            return false;
        }
        if (! in_array($now->dayOfWeek, $this->days ?? [])) {
            return false;
        }

        $current = $now->format('H:i:s');
        if ($current < $this->normalizeTime($this->start_time) || $current > $this->normalizeTime($this->end_time)) {
            return false;
        }

        return $shiftId !== null && $this->shifts->contains('id', $shiftId);
    }

    private function normalizeTime(string $time): string
    {
        return strlen($time) === 5 ? $time . ':00' : $time;
    }
}
