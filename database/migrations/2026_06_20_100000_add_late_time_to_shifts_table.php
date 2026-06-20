<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * وقت بداية اعتبار الحضور تأخيراً لكل فترة (مثلاً 08:15).
     * أي حضور بعد هذا الوقت يُخصم منه نقاط التأخير.
     */
    public function up(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->time('late_time')->nullable()->after('end_time');
        });
    }

    public function down(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropColumn('late_time');
        });
    }
};
