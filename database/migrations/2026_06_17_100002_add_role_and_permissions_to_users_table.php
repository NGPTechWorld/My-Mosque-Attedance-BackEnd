<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * صلاحيات لوحة الإدارة: الدور (مدير/مشرف) + الأقسام المسموحة + الفترات التي يديرها المشرف.
     * المستخدمون الحاليون يصبحون "admin" تلقائياً (وصول كامل).
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('admin')->after('password');   // admin | supervisor
            $table->json('permissions')->nullable()->after('role');        // الأقسام المسموحة للمشرف
            $table->json('shift_ids')->nullable()->after('permissions');   // الفترات التي يديرها المشرف
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'permissions', 'shift_ids']);
        });
    }
};
