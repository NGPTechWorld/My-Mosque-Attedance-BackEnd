<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventAttendance extends Model
{
    protected $fillable = [
        'attendance_event_id',
        'student_id',
        'date',
        'check_in_time',
    ];

    public function event()
    {
        return $this->belongsTo(AttendanceEvent::class, 'attendance_event_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
