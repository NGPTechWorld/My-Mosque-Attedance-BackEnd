<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('point_reasons', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // اسم السبب المعروض
            $table->enum('type', ['add', 'remove']);         // إضافة أو حذف
            $table->unsignedInteger('amount');               // الكمية الثابتة (موجبة دائماً)
            $table->boolean('active')->default(true);        // مفعّل/معطّل
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_reasons');
    }
};
