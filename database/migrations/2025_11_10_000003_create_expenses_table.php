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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('date')->comment('経費発生日');
            $table->string('category')->comment('費目');
            $table->decimal('amount', 10, 2)->comment('金額');
            $table->string('description')->comment('内容');
            $table->string('receipt_path')->nullable()->comment('レシート画像パス');
            $table->tinyInteger('status')->default(0)->comment('ステータス（0=申請中,1=承認済,2=差戻）');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};

