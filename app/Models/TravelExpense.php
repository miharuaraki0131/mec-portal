<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TravelExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'travel_request_id',
        'date',
        'description',
        'category',
        'cash',
        'ticket',
        'total',
        'remarks',
    ];

    protected $casts = [
        'date' => 'date',
        'cash' => 'decimal:2',
        'ticket' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // カテゴリ定数
    public const CATEGORY_TRANSPORTATION = '交通費';
    public const CATEGORY_ACCOMMODATION = '宿泊費';
    public const CATEGORY_DAILY_ALLOWANCE = '日当';
    public const CATEGORY_HALF_DAILY_ALLOWANCE = '半日当';
    public const CATEGORY_OTHER = 'その他';

    public function travelRequest(): BelongsTo
    {
        return $this->belongsTo(TravelRequest::class);
    }

    /**
     * 合計金額を自動計算
     */
    public function calculateTotal(): void
    {
        $this->total = $this->cash + $this->ticket;
    }

    /**
     * 保存時に自動計算
     */
    protected static function booted(): void
    {
        static::saving(function ($travelExpense) {
            $travelExpense->calculateTotal();
        });

        static::saved(function ($travelExpense) {
            // 小計を再計算
            $travelExpense->travelRequest->calculateSubtotal();
        });

        static::deleted(function ($travelExpense) {
            // 小計を再計算
            $travelExpense->travelRequest->calculateSubtotal();
        });
    }
}

