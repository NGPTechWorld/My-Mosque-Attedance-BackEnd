<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointTransaction extends Model
{
    protected $fillable = [
        'student_id',
        'type',         // add | remove
        'amount',       // الكمية (موجبة)
        'reason',       // السبب
        'balance_after',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
