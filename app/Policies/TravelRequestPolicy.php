<?php

namespace App\Policies;

use App\Models\TravelRequest;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TravelRequestPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // 全員が自分の申請一覧を見れる
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TravelRequest $travelRequest): bool
    {
        return $user->id === $travelRequest->user_id || $user->role === 1 || $user->role === 2; // 申請者、管理者、マネージャー
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // 全員が申請できる
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TravelRequest $travelRequest): bool
    {
        return ($user->id === $travelRequest->user_id && $travelRequest->status === TravelRequest::STATUS_PENDING) || $user->role === 1; // 申請者（申請中のみ）または管理者
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TravelRequest $travelRequest): bool
    {
        return ($user->id === $travelRequest->user_id && $travelRequest->status === TravelRequest::STATUS_PENDING) || $user->role === 1; // 申請者（申請中のみ）または管理者
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TravelRequest $travelRequest): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TravelRequest $travelRequest): bool
    {
        return false;
    }
}

