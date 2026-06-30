<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_event_id')->constrained('attendance_events')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->date('date');
            $table->time('check_in_time');
            $table->timestamps();
            $table->unique(['attendance_event_id', 'student_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_attendances');
    }
};
