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
        Schema::create('travel_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('destination')->comment('出張先');
            $table->string('purpose')->comment('目的');
            $table->date('departure_date')->comment('出発日');
            $table->date('return_date')->comment('帰着日');
            $table->decimal('subtotal', 10, 2)->default(0)->comment('小計');
            $table->decimal('advance_payment', 10, 2)->default(0)->comment('前払金');
            $table->decimal('settlement_amount', 10, 2)->default(0)->comment('精算金額');
            $table->tinyInteger('settlement_type')->default(0)->comment('0=返金,1=支給');
            $table->tinyInteger('status')->default(0)->comment('0=申請中,1=承認済,2=差戻');
            $table->timestamp('approved_at')->nullable()->comment('承認日時');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_requests');
    }
};

