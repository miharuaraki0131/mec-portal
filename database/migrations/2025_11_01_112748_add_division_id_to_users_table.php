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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('division_id')->nullable()->after('role')->comment('所属部署ID（主所属、null可）');
            $table->index('division_id');
        });
        
        // divisionsテーブルが存在する場合のみ外部キー制約を追加
        if (Schema::hasTable('divisions')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreign('division_id')->references('id')->on('divisions')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['division_id']);
            $table->dropIndex(['division_id']);
            $table->dropColumn('division_id');
        });
    }
};
