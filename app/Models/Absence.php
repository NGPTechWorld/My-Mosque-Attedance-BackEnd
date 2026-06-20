<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    protected $fillable = [
        'student_id',
        'date',
        'type', // excused | unexcused
    ];

    public const TYPES = [
        'excused' => 'مبرّر',
        'unexcused' => 'غير مبرّر',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }
}
