<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_events', function (Blueprint $table) {
            $table->id();
            $table->string('name');                     // اسم المناسبة
            $table->json('days');                       // أيام الأسبوع (0=الأحد..6=السبت)
            $table->time('start_time');                 // بداية قبول الحضور
            $table->time('end_time');                   // نهاية قبول الحضور
            $table->unsignedInteger('points')->default(0); // نقاط الحضور
            $table->string('message')->nullable();      // نص الإشعار + سبب النقاط
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_events');
    }
};
