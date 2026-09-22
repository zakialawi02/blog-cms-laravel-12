<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menyimpan artikel hasil generasi AI supaya job bisa di-retry tanpa
     * membuat artikel kedua (idempotensi) — lihat GenerateAiArticleJob.
     */
    public function up(): void
    {
        Schema::table('ai_article_generations', function (Blueprint $table) {
            $table->unsignedBigInteger('article_id')->nullable()->after('user_id');
            $table->index('article_id');
        });
    }

    public function down(): void
    {
        Schema::table('ai_article_generations', function (Blueprint $table) {
            $table->dropIndex(['article_id']);
            $table->dropColumn('article_id');
        });
    }
};
