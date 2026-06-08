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

    public function shift()
    {
        return $this->belongsTo(Shift::class);
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
