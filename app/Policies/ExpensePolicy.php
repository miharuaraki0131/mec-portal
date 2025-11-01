<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;

class ExpensePolicy
{
    /**
     * Determine if the user can view any expenses.
     */
    public function viewAny(User $user): bool
    {
        return true; // 全ユーザーが閲覧可能
    }

    /**
     * Determine if the user can view the expense.
     */
    public function view(User $user, Expense $expense): bool
    {
        // 管理者・マネージャーは全経費を閲覧可能
        if ($user->role >= 1) {
            return true;
        }
        
        // 一般ユーザーは自分の経費のみ閲覧可能
        return $expense->user_id === $user->id;
    }

    /**
     * Determine if the user can create expenses.
     */
    public function create(User $user): bool
    {
        return true; // 全ユーザーが申請可能
    }

    /**
     * Determine if the user can update the expense.
     */
    public function update(User $user, Expense $expense): bool
    {
        // 管理者は全経費を編集可能
        if ($user->role === 1) {
            return true;
        }
        
        // 一般ユーザー・マネージャーは申請中の自分の経費のみ編集可能
        return $expense->user_id === $user->id && $expense->status === Expense::STATUS_PENDING;
    }

    /**
     * Determine if the user can delete the expense.
     */
    public function delete(User $user, Expense $expense): bool
    {
        // 管理者は全経費を削除可能
        if ($user->role === 1) {
            return true;
        }
        
        // 一般ユーザー・マネージャーは申請中の自分の経費のみ削除可能
        return $expense->user_id === $user->id && $expense->status === Expense::STATUS_PENDING;
    }
}
