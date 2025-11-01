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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('資料タイトル');
            $table->string('file_path')->comment('S3パスまたはローカルパス');
            $table->string('file_type', 50)->comment('拡張子（PDF, XLSX, DOCXなど）');
            $table->string('category', 100)->nullable()->comment('分類');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade')->comment('アップロード者ID');
            $table->integer('view_count')->default(0)->comment('閲覧回数');
            $table->timestamps();
            
            // インデックス追加
            $table->index('category');
            $table->index('file_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};

