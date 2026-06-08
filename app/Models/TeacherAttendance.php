<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherAttendance extends Model
{
    protected $fillable = [
        'teacher_id',
        'date',
        'check_in_time',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
