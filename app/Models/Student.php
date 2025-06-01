<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
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
}
