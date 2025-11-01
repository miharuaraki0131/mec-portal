<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'category',
        'priority',
        'posted_by',
        'published_at',
        'view_count',
        'image_path',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'priority' => 'integer',
        'view_count' => 'integer',
    ];

    /**
     * 投稿者とのリレーション
     */
    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    /**
     * 公開済みのお知らせを取得
     */
    public function scopePublished($query)
    {
        return $query->where('published_at', '<=', now())
                    ->orWhereNull('published_at');
    }

    /**
     * 重要なお知らせを優先表示
     */
    public function scopePriority($query)
    {
        return $query->orderBy('priority', 'desc')
                    ->orderBy('published_at', 'desc');
    }

    /**
     * 閲覧数を増やす
     */
    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }
}
