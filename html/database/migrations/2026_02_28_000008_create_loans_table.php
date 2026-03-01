<?php
// database/migrations/2024_01_01_000008_create_loans_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('loan_number')->unique();
            $table->unsignedBigInteger('book_id');
            $table->unsignedBigInteger('reader_id');
            $table->date('loan_date');
            $table->date('due_date');
            $table->date('return_date')->nullable();
            $table->enum('status', ['active', 'returned', 'overdue', 'lost', 'damaged'])->default('active');
            $table->text('notes')->nullable();
            $table->decimal('fine_amount', 10, 2)->default(0);
            $table->boolean('fine_paid')->default(false);
            $table->unsignedBigInteger('issued_by')->nullable(); // librarian id
            $table->unsignedBigInteger('returned_by')->nullable(); // librarian id
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('book_id')
                ->references('id')
                ->on('books')
                ->onDelete('restrict');

            $table->foreign('reader_id')
                ->references('id')
                ->on('readers')
                ->onDelete('restrict');

            $table->index('loan_number');
            $table->index('status');
            $table->index('due_date');
            $table->index(['reader_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
