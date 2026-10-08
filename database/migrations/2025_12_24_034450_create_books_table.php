<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('isbn', 20)->nullable()->unique();
            $table->string('cover')->nullable();
            $table->string('author');
            $table->string('publisher')->nullable();
            $table->year('year');
            $table->text('description')->nullable();
            // Eksemplar fisik yang ada di rak dan bebas dipinjam siapa saja.
            // Eksemplar yang sedang ditahan untuk tiket atau antrean tidak dihitung di sini.
            $table->integer('stock')->default(0);
            $table->string('digital_link')->nullable();
            $table->integer('stock_online')->default(0);
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->integer('floor')->nullable()->default(1);
            $table->string('shelf_code')->nullable();
            $table->timestamps();
        });

        // Jaring pengaman terakhir: kalau ada jalur kode yang lupa mengunci baris,
        // database menolak stok minus alih-alih diam-diam menyimpannya.
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE books ADD CONSTRAINT books_stock_non_negative CHECK (stock >= 0 AND stock_online >= 0)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
