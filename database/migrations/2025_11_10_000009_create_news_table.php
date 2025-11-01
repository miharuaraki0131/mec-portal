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
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('タイトル');
            $table->text('content')->comment('本文');
            $table->string('category', 100)->nullable()->comment('カテゴリ（例: 社内報・メンテナンス）');
            $table->tinyInteger('priority')->default(0)->comment('優先度（0:通常, 1:重要）');
            $table->foreignId('posted_by')->constrained('users')->onDelete('cascade')->comment('投稿者ID');
            $table->dateTime('published_at')->nullable()->comment('公開日時');
            $table->integer('view_count')->default(0)->comment('閲覧数');
            $table->timestamps();
            
            // インデックス追加
            $table->index('published_at');
            $table->index('category');
            $table->index('priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};

