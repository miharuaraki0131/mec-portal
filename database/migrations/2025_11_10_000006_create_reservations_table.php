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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('room_name')->comment('会議室名');
            $table->dateTime('start_time')->comment('開始時刻');
            $table->dateTime('end_time')->comment('終了時刻');
            $table->string('description')->nullable()->comment('備考');
            $table->tinyInteger('status')->default(1)->comment('ステータス（1=有効,0=キャンセル）');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};

