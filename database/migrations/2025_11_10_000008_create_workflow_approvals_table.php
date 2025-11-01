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
        Schema::create('workflow_approvals', function (Blueprint $table) {
            $table->id();
            $table->string('request_type', 100)->comment('申請種別（travel, expenseなど）');
            $table->unsignedBigInteger('request_id')->comment('対象申請ID');
            $table->foreignId('applicant_id')->constrained('users')->onDelete('cascade')->comment('申請者ID');
            $table->integer('approval_order')->comment('承認順序（例：1=部署長, 2=経理）');
            $table->foreignId('approver_id')->nullable()->constrained('users')->onDelete('set null')->comment('承認者ID');
            $table->tinyInteger('is_final_approval')->default(0)->comment('1=最終承認');
            $table->tinyInteger('status')->default(0)->comment('0=未処理, 1=承認, 2=差戻');
            $table->text('comment')->nullable()->comment('コメント');
            $table->timestamp('approved_at')->nullable()->comment('承認日時');
            $table->timestamps();
            
            // インデックス追加
            $table->index(['request_type', 'request_id']);
            $table->index(['request_type', 'request_id', 'approval_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_approvals');
    }
};

