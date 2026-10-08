<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borrowings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('book_id')->constrained('books')->restrictOnDelete();
            $table->enum('type', ['online', 'offline']);
            // pending   : tiket dibuat, eksemplar ditahan, menunggu diambil di meja
            // active    : buku di tangan mahasiswa (atau akses e-book terbuka)
            // returned  : sudah kembali
            // expired   : tiket tidak diambil sampai pickup_expires_at
            // rejected  : ditolak admin
            // cancelled : dibatalkan mahasiswa sebelum diambil
            $table->enum('status', ['pending', 'active', 'returned', 'expired', 'rejected', 'cancelled'])->default('pending');
            $table->string('ticket_number')->unique();
            $table->timestamp('pickup_expires_at')->nullable();
            $table->timestamp('handed_over_at')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->unsignedTinyInteger('renewals')->default(0);
            // Denda dikunci saat buku kembali; selama masih dipinjam, denda dihitung berjalan.
            $table->unsignedInteger('fine_amount')->default(0);
            $table->timestamp('fine_paid_at')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'status']);
            $table->index(['book_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrowings');
    }
};
