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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['post', 'page'])->default('post')->nullable();
            $table->foreignId('category_id')->nullable()->constrained()->onUpdate('cascade')->nullOnDelete();
            $table->uuid('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->nullOnDelete();
            $table->string('title')->index();
            $table->longText('content')->nullable();
            $table->string('slug')->unique()->index();
            $table->string('excerpt', 2048)->nullable();
            $table->string('cover')->nullable()->comment('cover thumbnail image');
            $table->string('cover_large')->nullable();
            $table->enum('status', ['published', 'draft', 'pending'])->default('draft');
            $table->boolean('is_featured')->default(0);
            $table->string("meta_title")->nullable();
            $table->text("meta_desc", 300)->nullable();
            $table->text("meta_keywords", 255)->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('created_at')->useCurrent()->nullable();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
