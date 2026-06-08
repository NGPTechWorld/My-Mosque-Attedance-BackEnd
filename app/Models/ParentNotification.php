<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParentNotification extends Model
{
    protected $fillable = [
        'student_id',
        'guardian_phone',
        'type',
        'title',
        'body',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
