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
        Schema::create('divisions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('部署名');
            $table->unsignedBigInteger('manager_id')->nullable()->comment('部署責任者ID');
            $table->foreignId('parent_id')->nullable()->constrained('divisions')->onDelete('set null')->comment('親部署ID');
            $table->timestamps();
        });
        
        // usersテーブルが存在する場合のみ外部キー制約を追加
        if (Schema::hasTable('users')) {
            Schema::table('divisions', function (Blueprint $table) {
                $table->foreign('manager_id')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('divisions');
    }
};

