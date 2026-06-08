<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'code',           // كود الطالب اليدوي (يُستخدم في الـ QR)
        'name',
        'guardian_phone',
        'shift_id',
        'points',
    ];

    // العلاقة مع فترة الدوام
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    // العلاقة مع جدول الحضور
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // سجل عمليات النقاط (المحفظة)
    public function pointTransactions()
    {
        return $this->hasMany(PointTransaction::class)->latest();
    }

    // سجل الإشعارات الخاصة بالطالب
    public function notifications()
    {
        return $this->hasMany(ParentNotification::class)->latest();
    }

    /**
     * إيجاد الطالب عبر الكود اليدوي (المستخدم في الـ QR) أو الـ id الرقمي.
     */
    public static function resolveByCodeOrId($value): ?Student
    {
        $student = static::where('code', $value)->first();
        if ($student) {
            return $student;
        }
        return is_numeric($value) ? static::find($value) : null;
    }
}
