<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('cover')->nullable();
            $table->string('author');
            $table->year('year');
            $table->text('description')->nullable();
            $table->integer('stock')->default(0);
            $table->string('digital_link')->nullable();
            $table->integer('stock_online')->default(0);
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->integer('floor')->nullable()->default(1);
            $table->string('shelf_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
