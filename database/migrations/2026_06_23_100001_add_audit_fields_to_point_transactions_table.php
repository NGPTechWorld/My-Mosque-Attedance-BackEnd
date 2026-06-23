<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('point_transactions', function (Blueprint $table) {
            // الأستاذ الذي طبّق العملية (إن كانت من تطبيق الأساتذة)
            $table->foreignId('teacher_id')->nullable()->after('student_id')
                ->constrained('teachers')->nullOnDelete();
            // السبب الجاهز من برنامج النقاط
            $table->foreignId('point_reason_id')->nullable()->after('teacher_id')
                ->constrained('point_reasons')->nullOnDelete();
            // ملاحظة اختيارية يكتبها الأستاذ (تظهر في محفظة الأهل)
            $table->string('note')->nullable()->after('reason');
        });
    }

    public function down(): void
    {
        Schema::table('point_transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('teacher_id');
            $table->dropConstrainedForeignId('point_reason_id');
            $table->dropColumn('note');
        });
    }
};
