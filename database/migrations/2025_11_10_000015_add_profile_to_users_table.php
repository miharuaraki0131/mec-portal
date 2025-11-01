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
            $table->text('self_introduction')->nullable()->after('division_id')->comment('自己紹介・備考');
            $table->string('profile_image_path')->nullable()->after('self_introduction')->comment('プロフィール画像パス');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['self_introduction', 'profile_image_path']);
        });
    }
};

