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
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('product_name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            $table->string('currency')->default('USD');
            $table->decimal('price', 20, 2);
            $table->decimal('discount_price', 20, 2)->nullable();

            $table->string('thumbnail')->nullable()->comment('cover product'); // cover utama
            $table->integer('stock')->nullable()->comment('NULL = unlimited, number > 0 = stock'); // NULL = unlimited, angka > 0 = jumlah stok

            $table->boolean('is_published')->default(false);
            $table->integer('sales_count')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
