<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->string('phone')->nullable();
            $table->string('document')->nullable()->unique();
            $table->string('crm')->nullable()->unique();
            $table->string('specialty')->nullable()->index();
            $table->unsignedTinyInteger('experience_years')->default(0);
            $table->text('bio')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
