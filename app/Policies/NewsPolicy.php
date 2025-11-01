<?php

namespace App\Policies;

use App\Models\News;
use App\Models\User;

class NewsPolicy
{
    /**
     * お知らせの作成が許可されているか
     */
    public function create(User $user): bool
    {
        // 管理者（role: 1）のみ投稿可能
        return $user->role === 1;
    }

    /**
     * お知らせの更新が許可されているか
     */
    public function update(User $user, News $news): bool
    {
        // 管理者、または投稿者本人のみ更新可能
        return $user->role === 1 || $user->id === $news->posted_by;
    }

    /**
     * お知らせの削除が許可されているか
     */
    public function delete(User $user, News $news): bool
    {
        // 管理者、または投稿者本人のみ削除可能
        return $user->role === 1 || $user->id === $news->posted_by;
    }
}
