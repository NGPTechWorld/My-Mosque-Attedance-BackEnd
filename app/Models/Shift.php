<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $fillable = [
        'name',
        'days',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'days' => 'array', // تحويل JSON إلى array تلقائياً
    ];

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
