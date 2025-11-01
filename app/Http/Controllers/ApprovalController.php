<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApprovalRequest;
use App\Models\WorkflowApproval;
use App\Models\Expense;
use App\Models\TravelRequest;
use App\Models\Division;
use App\Models\User;
use App\Mail\ApprovalNotification;
use App\Mail\RejectionNotification;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ApprovalController extends Controller
{
    use LogsActivity;
    /**
     * 承認待ち件数を取得（API）
     */
    public function getPendingCount()
    {
        $user = Auth::user();
        
        // システム管理者は承認権限なし
        if ($user->role === 1) {
            return response()->json(['count' => 0]);
        }
        
        $isBusinessDivision = $this->isBusinessDivisionMember($user);
        $isDivisionManager = $this->isDivisionManager($user);

        // 承認権限がない場合は0を返す
        if (!$isBusinessDivision && !$isDivisionManager) {
            return response()->json(['count' => 0]);
        }

        $count = WorkflowApproval::with(['applicant', 'approver'])
            ->pending()
            ->where(function ($q) use ($user, $isBusinessDivision, $isDivisionManager) {
                if ($isBusinessDivision) {
                    $q->where(function ($subQ) use ($user) {
                        // 経費申請（業務部が承認者）かつ削除されていない
                        $subQ->where('request_type', 'expense')
                            ->where('approver_id', $user->id)
                            ->whereExists(function ($existsQuery) {
                                $existsQuery->selectRaw(1)
                                    ->from('expenses')
                                    ->whereColumn('expenses.id', 'workflow_approvals.request_id')
                                    ->whereNull('expenses.parent_id')
                                    ->where('expenses.approver_type', 'business');
                            });
                        
                        // 出張申請の最終承認（業務部が承認者）かつ削除されていない
                        $subQ->orWhere(function ($subQ2) use ($user) {
                            $subQ2->where('request_type', 'travel')
                                ->where('approver_id', $user->id)
                                ->where('is_final_approval', 1)
                                ->whereExists(function ($existsQuery) {
                                    $existsQuery->selectRaw(1)
                                        ->from('travel_requests')
                                        ->whereColumn('travel_requests.id', 'workflow_approvals.request_id');
                                });
                        });
                    });
                }
                
                if ($isDivisionManager) {
                    // ユーザーが管理者である部署IDを取得
                    $managedDivisionIds = Division::where('manager_id', $user->id)->pluck('id');
                    
                    $q->orWhere(function ($subQ) use ($user, $managedDivisionIds) {
                        // 経費申請（部門管理者が承認者）かつ削除されていない
                        $subQ->where('request_type', 'expense')
                            ->where('approver_id', $user->id)
                            ->whereExists(function ($existsQuery) use ($user) {
                                $existsQuery->selectRaw(1)
                                    ->from('expenses')
                                    ->whereColumn('expenses.id', 'workflow_approvals.request_id')
                                    ->whereNull('expenses.parent_id')
                                    ->where('expenses.approver_type', 'manager');
                            });
                        
                        // 出張申請の最初の承認（部門管理者が承認者）かつ削除されていない
                        $subQ->orWhere(function ($subQ2) use ($user, $managedDivisionIds) {
                            $subQ2->where('request_type', 'travel')
                                ->where('approver_id', $user->id)
                                ->where('is_final_approval', 0)
                                ->whereExists(function ($existsQuery) use ($user, $managedDivisionIds) {
                                    $existsQuery->selectRaw(1)
                                        ->from('travel_requests')
                                        ->join('users', 'travel_requests.user_id', '=', 'users.id')
                                        ->whereColumn('travel_requests.id', 'workflow_approvals.request_id')
                                        ->whereIn('users.division_id', $managedDivisionIds);
                                });
                        });
                    });
                }
            })
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * 承認待ち一覧
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // システム管理者は承認権限なし
        if ($user->role === 1) {
            abort(403, 'システム管理者に承認権限はありません。');
        }
        
        // 業務部のユーザーまたは部門管理者が承認可能
        $isBusinessDivision = $this->isBusinessDivisionMember($user);
        $isDivisionManager = $this->isDivisionManager($user);

        $query = WorkflowApproval::with(['applicant', 'approver'])
            ->pending()
            ->where(function ($q) use ($user, $isBusinessDivision, $isDivisionManager) {
                // 業務部の場合は経費申請と出張申請の最終承認が可能
                if ($isBusinessDivision) {
                    $q->where(function ($subQ) use ($user) {
                        // 経費申請（業務部が承認者）かつ削除されていない
                        $subQ->where('request_type', 'expense')
                            ->where('approver_id', $user->id)
                            ->whereExists(function ($existsQuery) {
                                $existsQuery->selectRaw(1)
                                    ->from('expenses')
                                    ->whereColumn('expenses.id', 'workflow_approvals.request_id')
                                    ->whereNull('expenses.parent_id')
                                    ->where('expenses.approver_type', 'business');
                            });
                        
                        // 出張申請の最終承認（業務部が承認者）かつ削除されていない
                        $subQ->orWhere(function ($subQ2) use ($user) {
                            $subQ2->where('request_type', 'travel')
                                ->where('approver_id', $user->id)
                                ->where('is_final_approval', 1)
                                ->whereExists(function ($existsQuery) {
                                    $existsQuery->selectRaw(1)
                                        ->from('travel_requests')
                                        ->whereColumn('travel_requests.id', 'workflow_approvals.request_id');
                                });
                        });
                    });
                }
                
                // 部門管理者の場合は経費申請と出張申請の最初の承認が可能かつ削除されていない
                if ($isDivisionManager) {
                    $managedDivisionIds = Division::where('manager_id', $user->id)->pluck('id');
                    
                    $q->orWhere(function ($subQ) use ($user, $managedDivisionIds) {
                        // 経費申請（部門管理者が承認者）かつ削除されていない
                        $subQ->where('request_type', 'expense')
                            ->where('approver_id', $user->id)
                            ->whereExists(function ($existsQuery) use ($user) {
                                $existsQuery->selectRaw(1)
                                    ->from('expenses')
                                    ->whereColumn('expenses.id', 'workflow_approvals.request_id')
                                    ->whereNull('expenses.parent_id')
                                    ->where('expenses.approver_type', 'manager');
                            });
                        
                        // 出張申請の最初の承認（部門管理者が承認者）かつ削除されていない
                        $subQ->orWhere(function ($subQ2) use ($user, $managedDivisionIds) {
                            $subQ2->where('request_type', 'travel')
                                ->where('approver_id', $user->id)
                                ->where('is_final_approval', 0)
                                ->whereExists(function ($existsQuery) use ($user, $managedDivisionIds) {
                                    $existsQuery->selectRaw(1)
                                        ->from('travel_requests')
                                        ->join('users', 'travel_requests.user_id', '=', 'users.id')
                                        ->whereColumn('travel_requests.id', 'workflow_approvals.request_id')
                                        ->whereIn('users.division_id', $managedDivisionIds);
                                });
                        });
                    });
                }
            });

        // 申請タイプでフィルタ
        if ($request->has('type') && $request->type) {
            $query->where('request_type', $request->type);
        }

        $approvals = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('approvals.index', compact('approvals', 'isBusinessDivision', 'isDivisionManager'));
    }

    /**
     * 承認
     */
    public function approve(ApprovalRequest $request, WorkflowApproval $approval)
    {
        $user = Auth::user();
        
        // 承認権限チェック
        if ($approval->approver_id !== $user->id || $approval->status !== WorkflowApproval::STATUS_PENDING) {
            abort(403, 'この申請を承認する権限がありません。');
        }

        $validated = $request->validated();

        // トランザクション処理
        DB::transaction(function () use ($approval, $validated) {
            // 承認処理
            $approval->update([
                'status' => WorkflowApproval::STATUS_APPROVED,
                'comment' => $validated['comment'] ?? null,
                'approved_at' => now(),
            ]);

            // 申請の状態を更新
            if ($approval->request_type === 'expense') {
                $expense = Expense::find($approval->request_id);
                if ($expense && $approval->is_final_approval) {
                    $expense->update([
                        'status' => Expense::STATUS_APPROVED,
                    ]);
                }
            } elseif ($approval->request_type === 'travel') {
                $travelRequest = TravelRequest::find($approval->request_id);
                if ($travelRequest) {
                    if ($approval->is_final_approval) {
                        // 最終承認の場合は承認済み
                        $travelRequest->update([
                            'status' => TravelRequest::STATUS_APPROVED,
                            'approved_at' => now(),
                        ]);
                    } else {
                        // 中間承認の場合は次の承認ステップを作成
                        $this->createNextApprovalStep($travelRequest);
                    }
                }
            }
        });

        // ログ記録
        $this->logApproval(
            $approval->request_type,
            $approval->request_id,
            'approved',
            $validated['comment'] ?? null
        );

        // トランザクション外でメール通知
        try {
            Mail::to($approval->applicant->email)->send(new ApprovalNotification($approval));
        } catch (\Exception $e) {
            Log::error('メール送信エラー: ' . $e->getMessage());
            return redirect()->route('approvals.index')
                ->with('warning', '承認は完了しましたが、メール送信でエラーが発生しました。');
        }

        return redirect()->route('approvals.index')
            ->with('success', '承認しました。申請者に通知しました。');
    }

    /**
     * 差戻
     */
    public function reject(ApprovalRequest $request, WorkflowApproval $approval)
    {
        $user = Auth::user();
        
        // 承認権限チェック
        if ($approval->approver_id !== $user->id || $approval->status !== WorkflowApproval::STATUS_PENDING) {
            abort(403, 'この申請を差戻す権限がありません。');
        }

        $validated = $request->validated();

        // トランザクション処理
        DB::transaction(function () use ($approval, $validated) {
            // 差戻処理
            $approval->update([
                'status' => WorkflowApproval::STATUS_REJECTED,
                'comment' => $validated['comment'],
                'approved_at' => now(),
            ]);

            // 申請の状態を差戻に更新
            if ($approval->request_type === 'expense') {
                $expense = Expense::find($approval->request_id);
                if ($expense) {
                    $expense->update([
                        'status' => Expense::STATUS_REJECTED,
                    ]);
                }
            } elseif ($approval->request_type === 'travel') {
                $travelRequest = TravelRequest::find($approval->request_id);
                if ($travelRequest) {
                    $travelRequest->update([
                        'status' => TravelRequest::STATUS_REJECTED,
                    ]);
                }
            }
        });

        // ログ記録
        $this->logApproval(
            $approval->request_type,
            $approval->request_id,
            'rejected',
            $validated['comment'] ?? null
        );

        // トランザクション外でメール通知
        try {
            Mail::to($approval->applicant->email)->send(new RejectionNotification($approval));
        } catch (\Exception $e) {
            Log::error('メール送信エラー: ' . $e->getMessage());
            return redirect()->route('approvals.index')
                ->with('warning', '差戻は完了しましたが、メール送信でエラーが発生しました。');
        }

        return redirect()->route('approvals.index')
            ->with('success', '差戻しました。申請者に通知しました。');
    }

    /**
     * 業務部のメンバーかどうか
     */
    private function isBusinessDivisionMember(User $user): bool
    {
        $businessDivision = Division::where('name', '業務部')->whereNull('parent_id')->first();
        if (!$businessDivision) {
            return false;
        }

        // 業務部または業務課に所属
        return $user->division_id === $businessDivision->id ||
               ($businessDivision->children && $businessDivision->children->pluck('id')->contains($user->division_id));
    }

    /**
     * ユーザーが部門管理者かどうかを判定
     */
    private function isDivisionManager(User $user): bool
    {
        return Division::where('manager_id', $user->id)->exists();
    }

    /**
     * 出張申請の次の承認ステップを作成（部署責任者承認後）
     */
    private function createNextApprovalStep(TravelRequest $travelRequest): void
    {
        $businessDivision = Division::where('name', '業務部')->whereNull('parent_id')->first();
        if (!$businessDivision) {
            return;
        }

        // 業務部のユーザー全員に最終承認権限を付与
        $businessUsers = User::where('delete_flg', 0)
            ->where(function ($query) use ($businessDivision) {
                $query->where('division_id', $businessDivision->id)
                    ->orWhereIn('division_id', $businessDivision->children->pluck('id'));
            })
            ->get();

        foreach ($businessUsers as $businessUser) {
            WorkflowApproval::create([
                'request_type' => 'travel',
                'request_id' => $travelRequest->id,
                'applicant_id' => $travelRequest->user_id,
                'approval_order' => 2,
                'approver_id' => $businessUser->id,
                'is_final_approval' => 1,
                'status' => WorkflowApproval::STATUS_PENDING,
            ]);
        }
    }
}

