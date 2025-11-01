<?php

namespace App\Policies;

use App\Models\FAQ;
use App\Models\User;

class FAQPolicy
{
    /**
     * FAQの作成が許可されているか
     */
    public function create(User $user): bool
    {
        // 管理者（role: 1）のみ作成可能
        return $user->role === 1;
    }

    /**
     * FAQの更新が許可されているか
     */
    public function update(User $user, FAQ $faq): bool
    {
        // 管理者のみ更新可能
        return $user->role === 1;
    }

    /**
     * FAQの削除が許可されているか
     */
    public function delete(User $user, FAQ $faq): bool
    {
        // 管理者のみ削除可能
        return $user->role === 1;
    }
}
