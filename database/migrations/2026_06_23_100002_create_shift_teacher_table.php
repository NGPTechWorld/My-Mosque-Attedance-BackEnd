<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shift_teacher', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->foreignId('shift_id')->constrained('shifts')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['teacher_id', 'shift_id']);
        });

        // نقل الفترات الحالية (shift_id المفرد) إلى العلاقة المتعددة
        DB::table('teachers')->whereNotNull('shift_id')->orderBy('id')
            ->each(function ($teacher) {
                DB::table('shift_teacher')->insert([
                    'teacher_id' => $teacher->id,
                    'shift_id' => $teacher->shift_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('shift_teacher');
    }
};
