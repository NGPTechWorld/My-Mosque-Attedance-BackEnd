<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            // ربط الأستاذ بفترة لمعرفة أيام دوامه المتوقّعة (الحضور/الغياب)
            $table->foreignId('shift_id')->nullable()->after('subject')
                ->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('shift_id');
        });
    }
};
