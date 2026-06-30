<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_event_shift', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_event_id')->constrained('attendance_events')->cascadeOnDelete();
            $table->foreignId('shift_id')->constrained('shifts')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['attendance_event_id', 'shift_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_event_shift');
    }
};
