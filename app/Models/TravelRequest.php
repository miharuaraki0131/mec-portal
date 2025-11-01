<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use App\Models\WorkflowApproval;

class TravelRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'destination',
        'purpose',
        'departure_date',
        'return_date',
        'subtotal',
        'advance_payment',
        'settlement_amount',
        'settlement_type',
        'status',
        'approved_at',
        'excel_path',
        'approver_type',
    ];

    protected $casts = [
        'departure_date' => 'date',
        'return_date' => 'date',
        'subtotal' => 'decimal:2',
        'advance_payment' => 'decimal:2',
        'settlement_amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    // ステータス定数
    public const STATUS_PENDING = 0;
    public const STATUS_APPROVED = 1;
    public const STATUS_REJECTED = 2;

    // 精算種別定数
    public const SETTLEMENT_TYPE_REFUND = 0; // 返金
    public const SETTLEMENT_TYPE_PAYMENT = 1; // 支給

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function travelExpenses(): HasMany
    {
        return $this->hasMany(TravelExpense::class);
    }

    /**
     * 承認フロー
     */
    public function workflowApprovals(): HasMany
    {
        return $this->hasMany(WorkflowApproval::class, 'request_id')
            ->where('request_type', 'travel')
            ->orderBy('approval_order');
    }

    /**
     * 現在の承認待ち
     */
    public function pendingApproval(): ?WorkflowApproval
    {
        return $this->workflowApprovals()
            ->where('status', WorkflowApproval::STATUS_PENDING)
            ->orderBy('approval_order')
            ->first();
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => '申請中',
            self::STATUS_APPROVED => '承認済',
            self::STATUS_REJECTED => '差戻',
            default => '不明',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'blue',
            self::STATUS_APPROVED => 'green',
            self::STATUS_REJECTED => 'red',
            default => 'gray',
        };
    }

    public function getSettlementTypeLabelAttribute(): string
    {
        return match ($this->settlement_type) {
            self::SETTLEMENT_TYPE_REFUND => '返金',
            self::SETTLEMENT_TYPE_PAYMENT => '支給',
            default => '不明',
        };
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * 小計を計算（経費明細の合計から自動計算）
     */
    public function calculateSubtotal(): void
    {
        $this->subtotal = $this->travelExpenses()->sum('total');
        $this->settlement_amount = $this->subtotal - $this->advance_payment;
        
        // 精算金額が負の場合は返金、正の場合は支給
        $this->settlement_type = $this->settlement_amount >= 0 
            ? self::SETTLEMENT_TYPE_PAYMENT 
            : self::SETTLEMENT_TYPE_REFUND;
            
        $this->save();
    }
}

