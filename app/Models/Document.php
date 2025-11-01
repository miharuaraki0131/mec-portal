<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'file_path',
        'file_type',
        'category',
        'division_id',
        'uploaded_by',
        'view_count',
    ];

    protected $casts = [
        'view_count' => 'integer',
    ];

    /**
     * アップロード者とのリレーション
     */
    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * 部署とのリレーション
     */
    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    /**
     * 閲覧数を増やす
     */
    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    /**
     * ファイルのURLを取得
     */
    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    /**
     * ファイルサイズを取得
     */
    public function getFileSizeAttribute(): ?string
    {
        $path = storage_path('app/public/' . $this->file_path);
        if (file_exists($path)) {
            $bytes = filesize($path);
            $units = ['B', 'KB', 'MB', 'GB'];
            for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
                $bytes /= 1024;
            }
            return round($bytes, 2) . ' ' . $units[$i];
        }
        return null;
    }

    /**
     * 部署名を取得（nullの場合は「全般」）
     */
    public function getDivisionNameAttribute(): string
    {
        return $this->division ? $this->division->name : '全般';
    }
}
