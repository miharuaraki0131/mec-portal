<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    /**
     * ドキュメントの作成が許可されているか
     */
    public function create(User $user): bool
    {
        // 管理者（role: 1）のみアップロード可能
        return $user->role === 1;
    }

    /**
     * ドキュメントの更新が許可されているか
     */
    public function update(User $user, Document $document): bool
    {
        // 管理者、またはアップロード者本人のみ更新可能
        return $user->role === 1 || $user->id === $document->uploaded_by;
    }

    /**
     * ドキュメントの削除が許可されているか
     */
    public function delete(User $user, Document $document): bool
    {
        // 管理者、またはアップロード者本人のみ削除可能
        return $user->role === 1 || $user->id === $document->uploaded_by;
    }
}
