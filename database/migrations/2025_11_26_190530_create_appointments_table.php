<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_schedule_id')->nullable()->constrained()->nullOnDelete();
            $table->date('scheduled_date');
            $table->time('scheduled_time');
            $table->enum('status', ['scheduled', 'confirmed', 'completed', 'canceled'])->default('scheduled')->index();
            $table->text('notes')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['doctor_id', 'scheduled_date']);
            $table->index(['patient_id', 'scheduled_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
