<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subject',
        'message',
        'department',
        'parent_id',
        'thread_id',
        'replied_by',
        'replied_at',
        'status',
    ];

    protected $casts = [
        'replied_at' => 'datetime',
        'status' => 'integer',
    ];

    // ステータス定数
    const STATUS_PENDING = 0;    // 未対応
    const STATUS_IN_PROGRESS = 1; // 対応中
    const STATUS_RESOLVED = 2;    // 対応済

    /**
     * 送信者（ユーザー）
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 返信者（ユーザー）
     */
    public function repliedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'replied_by');
    }

    /**
     * 親問い合わせ
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Inquiry::class, 'parent_id');
    }

    /**
     * 返信一覧
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Inquiry::class, 'parent_id')->orderBy('created_at');
    }

    /**
     * スレッド内のすべてのメッセージ
     */
    public function threadMessages(): HasMany
    {
        return $this->hasMany(Inquiry::class, 'thread_id')->orderBy('created_at');
    }

    /**
     * ステータスラベル取得
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => '未対応',
            self::STATUS_IN_PROGRESS => '対応中',
            self::STATUS_RESOLVED => '対応済',
            default => '不明',
        };
    }

    /**
     * ステータスカラー取得
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'red',
            self::STATUS_IN_PROGRESS => 'yellow',
            self::STATUS_RESOLVED => 'green',
            default => 'gray',
        };
    }

    /**
     * 未対応でフィルタリング
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * 対応中でフィルタリング
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    /**
     * 対応済でフィルタリング
     */
    public function scopeResolved($query)
    {
        return $query->where('status', self::STATUS_RESOLVED);
    }

    /**
     * 部署でフィルタリング
     */
    public function scopeByDepartment($query, ?string $department)
    {
        if ($department) {
            return $query->where('department', $department);
        }
        return $query;
    }

    /**
     * 自分の問い合わせのみ
     */
    public function scopeMine($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * 返信でない（最初の投稿のみ）
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * スレッドIDを生成（新規問い合わせ用）
     */
    public static function generateThreadId(): int
    {
        return time();
    }
}

