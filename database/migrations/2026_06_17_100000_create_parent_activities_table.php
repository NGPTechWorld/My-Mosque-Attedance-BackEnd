<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * سجل تفاعل تطبيق الأهل مع النظام (دخول، فتح التطبيق، عرض النقاط/الإشعارات...).
     * يُستخدم في قسم "متابعة النظام" بلوحة الإدارة.
     */
    public function up(): void
    {
        Schema::create('parent_activities', function (Blueprint $table) {
            $table->id();
            $table->string('guardian_phone')->index();
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();
            $table->string('student_name')->nullable();   // نحتفظ بالاسم حتى لو حُذف الطالب
            $table->string('action');                      // login | open_app | view_points | view_notifications | device
            $table->string('description')->nullable();     // وصف عربي للعملية
            $table->string('platform')->nullable();        // android | ios
            $table->timestamps();

            $table->index(['action', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parent_activities');
    }
};
