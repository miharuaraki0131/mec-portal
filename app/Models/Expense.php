<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\WorkflowApproval;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'parent_id',
        'date',
        'period_from',
        'period_to',
        'category',
        'is_transportation',
        'amount',
        'description',
        'vehicle',
        'route_from',
        'route_via',
        'route_to',
        'transportation_type',
        'receipt_path',
        'excel_path',
        'status',
        'approver_type',
    ];

    protected $casts = [
        'date' => 'date',
        'period_from' => 'date',
        'period_to' => 'date',
        'amount' => 'decimal:2',
        'status' => 'integer',
        'is_transportation' => 'boolean',
    ];

    // ステータス定数
    const STATUS_PENDING = 0;    // 申請中
    const STATUS_APPROVED = 1;    // 承認済
    const STATUS_REJECTED = 2;    // 差戻

    /**
     * 申請者（ユーザー）
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 親経費（交通費申請の親レコード）
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Expense::class, 'parent_id');
    }

    /**
     * 子経費（交通費申請の明細）
     */
    public function children(): HasMany
    {
        return $this->hasMany(Expense::class, 'parent_id');
    }

    /**
     * 承認フロー
     */
    public function workflowApprovals(): HasMany
    {
        return $this->hasMany(WorkflowApproval::class, 'request_id')
            ->where('request_type', 'expense')
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

    /**
     * 交通費申請かどうか
     */
    public function isTransportation(): bool
    {
        return $this->is_transportation && $this->parent_id === null;
    }

    /**
     * 交通費明細かどうか
     */
    public function isTransportationItem(): bool
    {
        return $this->parent_id !== null;
    }

    /**
     * 合計金額を計算（交通費申請の場合、子レコードの合計）
     */
    public function getTotalAmountAttribute(): float
    {
        if ($this->isTransportation() && $this->children->count() > 0) {
            return $this->children->sum('amount');
        }
        return $this->amount;
    }

    /**
     * ステータスラベル取得
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => '申請中',
            self::STATUS_APPROVED => '承認済',
            self::STATUS_REJECTED => '差戻',
            default => '不明',
        };
    }

    /**
     * ステータスカラー取得
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'yellow',
            self::STATUS_APPROVED => 'green',
            self::STATUS_REJECTED => 'red',
            default => 'gray',
        };
    }

    /**
     * 申請中でフィルタリング
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * 承認済でフィルタリング
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }
}
