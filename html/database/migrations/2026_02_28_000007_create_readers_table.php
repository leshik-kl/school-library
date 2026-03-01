<?php
// database/migrations/2024_01_01_000007_create_readers_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('readers', function (Blueprint $table) {
            $table->id();
            $table->string('library_card_number')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->date('birth_date');
            $table->enum('class', ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', 'teacher', 'staff'])->default('1');
            $table->string('phone')->nullable();
            $table->string('email')->unique();
            $table->string('address')->nullable();
            $table->string('photo')->nullable();
            $table->date('registration_date');
            $table->date('expiry_date');
            $table->enum('status', ['active', 'blocked', 'expired'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('library_card_number');
            $table->index('last_name');
            $table->index('status');
            $table->index('expiry_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('readers');
    }
};
