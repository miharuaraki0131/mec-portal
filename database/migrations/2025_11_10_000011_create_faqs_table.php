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
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->text('question')->comment('質問');
            $table->text('answer')->comment('回答');
            $table->string('category', 100)->nullable()->comment('分類');
            $table->integer('helpful_count')->default(0)->comment('「役に立った」数');
            $table->integer('view_count')->default(0)->comment('閲覧数');
            $table->json('embedding_vector')->nullable()->comment('AI検索用ベクトル');
            $table->timestamps();
            
            // インデックス追加
            $table->index('category');
            $table->fullText(['question', 'answer']); // 全文検索用
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faqs');
    }
};

