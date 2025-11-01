<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FAQ extends Model
{
    use HasFactory;

    protected $table = 'faqs';

    protected $fillable = [
        'question',
        'answer',
        'category',
        'helpful_count',
        'view_count',
        'embedding_vector',
    ];

    protected $casts = [
        'helpful_count' => 'integer',
        'view_count' => 'integer',
        'embedding_vector' => 'array',
    ];

    /**
     * 閲覧数を増やす
     */
    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    /**
     * 「役に立った」数を増やす
     */
    public function incrementHelpfulCount(): void
    {
        $this->increment('helpful_count');
    }

    /**
     * カテゴリでフィルタリング
     */
    public function scopeByCategory($query, ?string $category)
    {
        if ($category) {
            return $query->where('category', $category);
        }
        return $query;
    }

    /**
     * 検索（質問・回答から検索）
     */
    public function scopeSearch($query, ?string $keyword)
    {
        if ($keyword) {
            return $query->where(function ($q) use ($keyword) {
                $q->where('question', 'like', '%' . $keyword . '%')
                  ->orWhere('answer', 'like', '%' . $keyword . '%');
            });
        }
        return $query;
    }
}
