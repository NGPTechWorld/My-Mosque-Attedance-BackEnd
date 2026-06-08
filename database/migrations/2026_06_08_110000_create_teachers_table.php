<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable()->unique(); // كود الأستاذ للـ QR
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('subject')->nullable();         // المادة / الحلقة
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
