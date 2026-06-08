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
        Schema::create('device_tokens', function (Blueprint $table) {
            $table->id();
            // FCM token قد يتجاوز 255 حرفاً، لذلك نتجنب فهرس فريد على الطول الكامل
            // ونعتمد على updateOrCreate في الكود لتفادي التكرار.
            $table->string('token', 512);                   // FCM token الخاص بجهاز الأهل
            $table->string('guardian_phone')->index();      // رقم الأهل المرتبط بالجهاز
            $table->string('platform')->nullable();         // android / ios
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_tokens');
    }
};
