<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('parent_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('guardian_phone')->index();   // لتسهيل جلب إشعارات الأهل
            $table->string('type')->default('attendance'); // نوع الإشعار
            $table->string('title');
            $table->text('body');
            $table->json('data')->nullable();             // بيانات إضافية (تاريخ، وقت...)
            $table->timestamp('read_at')->nullable();     // متى قُرئ الإشعار
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parent_notifications');
    }
};
