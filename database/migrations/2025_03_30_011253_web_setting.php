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
        Schema::create('web_settings', function (Blueprint $table) { // Ganti nama tabel jika lebih umum
            $table->id();
            $table->string('key', 191)->unique(); // Nama unik untuk setting
            $table->text('value')->nullable();     // Nilai setting
            $table->string('type', 50)->default('string'); // Tipe data: string, text, integer, boolean, json, array, dll.
            $table->timestamp('created_at')->useCurrent()->nullable();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('web_settings');
    }
};
