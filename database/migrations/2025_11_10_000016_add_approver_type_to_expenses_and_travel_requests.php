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
        Schema::table('expenses', function (Blueprint $table) {
            $table->enum('approver_type', ['business', 'manager'])->nullable()->after('status')->comment('承認者タイプ: business=業務部, manager=部門管理者');
        });

        Schema::table('travel_requests', function (Blueprint $table) {
            $table->enum('approver_type', ['business', 'manager'])->nullable()->after('status')->comment('承認者タイプ: business=業務部, manager=部門管理者');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('approver_type');
        });

        Schema::table('travel_requests', function (Blueprint $table) {
            $table->dropColumn('approver_type');
        });
    }
};

