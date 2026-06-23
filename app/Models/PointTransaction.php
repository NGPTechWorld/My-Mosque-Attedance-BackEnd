<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointTransaction extends Model
{
    protected $fillable = [
        'student_id',
        'teacher_id',       // الأستاذ الذي طبّق العملية (اختياري)
        'point_reason_id',  // السبب الجاهز من برنامج النقاط (اختياري)
        'type',             // add | remove
        'amount',           // الإجمالي (موجب) = قيمة السبب × الكمية
        'quantity',         // عدد الوحدات (مثلاً عدد الصفحات)
        'reason',           // السبب (نص)
        'note',             // ملاحظة الأستاذ (اختيارية)
        'balance_after',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function pointReason()
    {
        return $this->belongsTo(PointReason::class);
    }
}
