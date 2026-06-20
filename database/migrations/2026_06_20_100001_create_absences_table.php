<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * سجل غياب الطلاب: مبرّر (excused) أو غير مبرّر (unexcused).
     * المبرّر يُخصم منه نقاط، غير المبرّر لا.
     */
    public function up(): void
    {
        Schema::create('absences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->enum('type', ['excused', 'unexcused'])->default('unexcused');
            $table->timestamps();

            $table->unique(['student_id', 'date']); // غياب واحد لكل طالب في اليوم
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absences');
    }
};
