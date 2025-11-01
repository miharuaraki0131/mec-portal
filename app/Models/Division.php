<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Division extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'manager_id',
        'parent_id',
    ];

    /**
     * 管理者とのリレーション
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * 親部署とのリレーション
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'parent_id');
    }

    /**
     * 子部署とのリレーション
     */
    public function children(): HasMany
    {
        return $this->hasMany(Division::class, 'parent_id');
    }

    /**
     * この部署のドキュメント
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * この部署に所属するユーザー
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * フルパス名を取得（親部署名を含む）
     * 例: "第1事業部 > 開発1課"
     */
    public function getFullNameAttribute(): string
    {
        if ($this->parent) {
            return $this->parent->name . ' > ' . $this->name;
        }
        return $this->name;
    }

    /**
     * 親部署のみを取得
     */
    public static function getParentDivisions()
    {
        return self::whereNull('parent_id')->orderBy('name')->get();
    }

    /**
     * 子部署（課）を含むすべての部署を階層構造で取得
     */
    public static function getHierarchical()
    {
        $parents = self::getParentDivisions();
        
        return $parents->map(function ($parent) {
            $parent->load('children');
            return $parent;
        });
    }
}
