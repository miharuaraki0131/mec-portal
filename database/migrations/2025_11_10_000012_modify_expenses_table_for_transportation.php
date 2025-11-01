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
            // 親レコードID（交通費申請の親子関係用）
            $table->foreignId('parent_id')->nullable()->after('user_id')->constrained('expenses')->onDelete('cascade')->comment('親経費ID（交通費申請の親）');
            
            // 交通費申請の期間
            $table->date('period_from')->nullable()->after('date')->comment('交通費申請期間（開始日）');
            $table->date('period_to')->nullable()->after('period_from')->comment('交通費申請期間（終了日）');
            
            // 交通費申請フラグ
            $table->boolean('is_transportation')->default(false)->after('category')->comment('交通費申請かどうか');
            
            // 交通費関連の詳細情報
            $table->string('vehicle')->nullable()->after('description')->comment('乗物');
            $table->string('route_from')->nullable()->after('vehicle')->comment('出発地');
            $table->string('route_via')->nullable()->after('route_from')->comment('経由地');
            $table->string('route_to')->nullable()->after('route_via')->comment('到着地');
            $table->enum('transportation_type', ['片道', '往復'])->nullable()->after('route_to')->comment('片道/往復');
            
            // インデックス
            $table->index('parent_id');
            $table->index('is_transportation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropIndex(['parent_id']);
            $table->dropIndex(['is_transportation']);
            $table->dropColumn([
                'parent_id',
                'period_from',
                'period_to',
                'is_transportation',
                'vehicle',
                'route_from',
                'route_via',
                'route_to',
                'transportation_type',
            ]);
        });
    }
};

