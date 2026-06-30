<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('point_reason_shift', function (Blueprint $table) {
            $table->id();
            $table->foreignId('point_reason_id')->constrained('point_reasons')->cascadeOnDelete();
            $table->foreignId('shift_id')->constrained('shifts')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['point_reason_id', 'shift_id']);
        });

        // ربط الأسباب الموجودة بكل الفترات الحالية (حتى تبقى متاحة كما كانت)
        $shiftIds = DB::table('shifts')->pluck('id');
        DB::table('point_reasons')->orderBy('id')->each(function ($reason) use ($shiftIds) {
            foreach ($shiftIds as $shiftId) {
                DB::table('point_reason_shift')->insert([
                    'point_reason_id' => $reason->id,
                    'shift_id' => $shiftId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_reason_shift');
    }
};
