<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Builder;

class WorkflowApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_type',
        'request_id',
        'applicant_id',
        'approval_order',
        'approver_id',
        'is_final_approval',
        'status',
        'comment',
        'approved_at',
    ];

    protected $casts = [
        'approval_order' => 'integer',
        'is_final_approval' => 'boolean',
        'status' => 'integer',
        'approved_at' => 'datetime',
    ];

    // ステータス定数
    public const STATUS_PENDING = 0;    // 未処理
    public const STATUS_APPROVED = 1;    // 承認
    public const STATUS_REJECTED = 2;     // 差戻

    /**
     * 申請者
     */
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'applicant_id');
    }

    /**
     * 承認者
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    /**
     * ステータスラベル取得
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => '承認待ち',
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
        return match ($this->status) {
            self::STATUS_PENDING => 'yellow',
            self::STATUS_APPROVED => 'green',
            self::STATUS_REJECTED => 'red',
            default => 'gray',
        };
    }

    /**
     * 承認待ちを取得
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * 特定ユーザーの承認待ちを取得
     */
    public function scopeForApprover(Builder $query, int $userId): Builder
    {
        return $query->where('approver_id', $userId)->pending();
    }

    /**
     * 申請タイプでフィルタ
     */
    public function scopeByRequestType(Builder $query, string $type): Builder
    {
        return $query->where('request_type', $type);
    }
}

