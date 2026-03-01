<?php
// database/migrations/2024_01_01_000004_create_books_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('isbn', 13)->unique();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('publisher_id')->nullable();
            $table->year('publication_year')->nullable();
            $table->integer('pages')->nullable();
            $table->string('language', 3)->default('ru');
            $table->string('cover_image')->nullable();
            $table->string('format')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->integer('quantity_total')->default(1);
            $table->integer('quantity_available')->default(1);
            $table->integer('quantity_lost')->default(0);
            $table->integer('quantity_damaged')->default(0);
            $table->string('location')->nullable();
            $table->string('shelf')->nullable();
            $table->date('acquisition_date')->nullable();
            $table->enum('status', ['available', 'reserved', 'repair', 'lost'])->default('available');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('publisher_id')
                ->references('id')
                ->on('publishers')
                ->onDelete('set null');

            $table->index('title');
            $table->index('isbn');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
