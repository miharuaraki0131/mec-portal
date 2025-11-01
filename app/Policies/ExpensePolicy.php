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
        // 申請者本人のみ、申請中または差し戻し状態の経費を編集可能（再申請可能）
        // 管理者・業務部・部署責任者も申請後の編集は不可（改ざん防止）
        return $expense->user_id === $user->id && 
               ($expense->status === Expense::STATUS_PENDING || $expense->status === Expense::STATUS_REJECTED);
    }

    /**
     * Determine if the user can delete the expense.
     */
    public function delete(User $user, Expense $expense): bool
    {
        // 申請者本人のみ、申請中または差し戻し状態の経費を削除可能
        // 管理者・業務部・部署責任者も申請後の削除は不可（改ざん防止）
        return $expense->user_id === $user->id && 
               ($expense->status === Expense::STATUS_PENDING || $expense->status === Expense::STATUS_REJECTED);
    }
}
