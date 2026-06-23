<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('point_transactions', function (Blueprint $table) {
            // عدد الوحدات (مثلاً عدد الصفحات) — يُضرب بقيمة السبب
            $table->unsignedInteger('quantity')->default(1)->after('amount');
        });
    }

    public function down(): void
    {
        Schema::table('point_transactions', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });
    }
};
