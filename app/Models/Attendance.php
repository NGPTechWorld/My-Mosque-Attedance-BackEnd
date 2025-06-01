<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'student_id',
        'date',
        'check_in_time',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
