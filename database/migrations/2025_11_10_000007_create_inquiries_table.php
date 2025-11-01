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
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('送信者ID');
            $table->string('subject')->comment('件名');
            $table->text('message')->comment('メッセージ');
            $table->string('department')->comment('送信先部署');
            $table->foreignId('parent_id')->nullable()->constrained('inquiries')->onDelete('cascade')->comment('親問い合わせID（返信の場合）');
            $table->unsignedBigInteger('thread_id')->nullable()->comment('スレッドID（同一スレッドをグループ化）');
            $table->foreignId('replied_by')->nullable()->constrained('users')->onDelete('set null')->comment('返信者ID');
            $table->timestamp('replied_at')->nullable()->comment('返信日時');
            $table->tinyInteger('status')->default(0)->comment('ステータス（0=未対応,1=対応中,2=対応済）');
            $table->timestamps();
            
            // インデックス追加
            $table->index('parent_id');
            $table->index('thread_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inquiries');
    }
};

