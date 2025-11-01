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
        Schema::create('travel_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('travel_request_id')->constrained('travel_requests')->onDelete('cascade');
            $table->date('date')->comment('日付');
            $table->string('description')->comment('内容');
            $table->enum('category', ['交通費', '宿泊費', '日当', '半日当', 'その他'])->comment('カテゴリ');
            $table->decimal('cash', 10, 2)->default(0)->comment('現金');
            $table->decimal('ticket', 10, 2)->default(0)->comment('チケット');
            $table->decimal('total', 10, 2)->default(0)->comment('合計');
            $table->text('remarks')->nullable()->comment('備考');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_expenses');
    }
};

