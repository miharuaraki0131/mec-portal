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
        // expensesテーブルにexcel_pathを追加
        Schema::table('expenses', function (Blueprint $table) {
            $table->string('excel_path')->nullable()->after('receipt_path')->comment('Excelファイルパス');
        });

        // travel_requestsテーブルにexcel_pathを追加
        Schema::table('travel_requests', function (Blueprint $table) {
            $table->string('excel_path')->nullable()->after('approved_at')->comment('Excelファイルパス');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('excel_path');
        });

        Schema::table('travel_requests', function (Blueprint $table) {
            $table->dropColumn('excel_path');
        });
    }
};

