<?php

namespace App\Policies;

use App\Models\Inquiry;
use App\Models\User;

class InquiryPolicy
{
    /**
     * Determine if the user can view any inquiries.
     */
    public function viewAny(User $user): bool
    {
        return true; // 全ユーザーが閲覧可能
    }

    /**
     * Determine if the user can view the inquiry.
     */
    public function view(User $user, Inquiry $inquiry): bool
    {
        // 管理者・マネージャーは全問い合わせを閲覧可能
        if ($user->role >= 1) {
            return true;
        }
        
        // 一般ユーザーは自分の問い合わせのみ閲覧可能
        return $inquiry->user_id === $user->id;
    }

    /**
     * Determine if the user can create inquiries.
     */
    public function create(User $user): bool
    {
        return true; // 全ユーザーが問い合わせ可能
    }

    /**
     * Determine if the user can update the inquiry.
     */
    public function update(User $user, Inquiry $inquiry): bool
    {
        // 管理者は全問い合わせを編集可能
        if ($user->role === 1) {
            return true;
        }
        
        // 一般ユーザー・マネージャーは自分の問い合わせのみ編集可能
        return $inquiry->user_id === $user->id;
    }

    /**
     * Determine if the user can delete the inquiry.
     */
    public function delete(User $user, Inquiry $inquiry): bool
    {
        // 管理者は全問い合わせを削除可能
        if ($user->role === 1) {
            return true;
        }
        
        // 一般ユーザー・マネージャーは自分の問い合わせのみ削除可能
        return $inquiry->user_id === $user->id;
    }

    /**
     * Determine if the user can reply to the inquiry.
     */
    public function reply(User $user, Inquiry $inquiry): bool
    {
        // 管理者・マネージャーは返信可能
        if ($user->role >= 1) {
            return true;
        }
        
        // 一般ユーザーは自分の問い合わせに返信可能
        return $inquiry->user_id === $user->id;
    }

    /**
     * Determine if the user can update the status of the inquiry.
     */
    public function updateStatus(User $user, Inquiry $inquiry): bool
    {
        // 管理者・マネージャーのみステータス更新可能
        return $user->role >= 1;
    }
}

