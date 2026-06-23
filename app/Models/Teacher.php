<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = [
        'code',
        'name',
        'phone',
        'subject',
        'shift_id',
    ];

    public function attendances()
    {
        return $this->hasMany(TeacherAttendance::class);
    }

    // الفترة الأساسية (للتوافق مع الكود القديم)
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    // فترات الأستاذ (متعددة) — أستاذ يمكن أن يدرّس في أكثر من فترة
    public function shifts()
    {
        return $this->belongsToMany(Shift::class, 'shift_teacher')->withTimestamps();
    }

    /** هل ينتمي الأستاذ لهذه الفترة؟ */
    public function teachesShift($shiftId): bool
    {
        if ($shiftId === null) {
            return false;
        }
        return $this->shifts->contains('id', $shiftId);
    }

    /**
     * إيجاد الأستاذ عبر الكود (QR) أو الـ id الرقمي.
     */
    public static function resolveByCodeOrId($value): ?Teacher
    {
        $teacher = static::where('code', $value)->first();
        if ($teacher) {
            return $teacher;
        }
        return is_numeric($value) ? static::find($value) : null;
    }
}
