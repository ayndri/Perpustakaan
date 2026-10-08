<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('book_id')->constrained('books')->cascadeOnDelete();
            // waiting   : di antrean, urutan ditentukan created_at
            // fulfilled : eksemplar sudah diberikan sebagai tiket (lihat borrowing_id)
            // cancelled : keluar dari antrean
            $table->enum('status', ['waiting', 'fulfilled', 'cancelled'])->default('waiting');
            $table->foreignId('borrowing_id')->nullable()->constrained('borrowings')->nullOnDelete();
            $table->timestamps();

            $table->index(['book_id', 'status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
