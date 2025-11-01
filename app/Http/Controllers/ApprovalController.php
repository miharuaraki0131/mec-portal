<?php

namespace App\Http\Controllers;

use App\Models\WorkflowApproval;
use App\Models\Expense;
use App\Models\TravelRequest;
use App\Models\Division;
use App\Models\User;
use App\Mail\ApprovalNotification;
use App\Mail\RejectionNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ApprovalController extends Controller
{
    /**
     * 承認待ち一覧
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // 業務部のユーザーまたは部署責任者（role=2）が承認可能
        $isBusinessDivision = $this->isBusinessDivisionMember($user);
        $isManager = $user->role === 2; // manager

        $query = WorkflowApproval::with(['applicant', 'approver'])
            ->pending()
            ->where(function ($q) use ($user, $isBusinessDivision, $isManager) {
                // 業務部の場合は経費申請と出張申請の最終承認が可能
                if ($isBusinessDivision) {
                    $q->where(function ($subQ) use ($user) {
                        // 経費申請（業務部が承認者）
                        $subQ->where('request_type', 'expense')
                            ->where('approver_id', $user->id);
                        
                        // 出張申請の最終承認（業務部が承認者）
                        $subQ->orWhere(function ($subQ2) use ($user) {
                            $subQ2->where('request_type', 'travel')
                                ->where('approver_id', $user->id)
                                ->where('is_final_approval', 1);
                        });
                    });
                }
                
                // 部署責任者の場合は出張申請の最初の承認が可能
                if ($isManager) {
                    $q->orWhere(function ($subQ) use ($user) {
                        $subQ->where('request_type', 'travel')
                            ->where('approver_id', $user->id)
                            ->where('is_final_approval', 0);
                    });
                }
            });

        // 申請タイプでフィルタ
        if ($request->has('type') && $request->type) {
            $query->where('request_type', $request->type);
        }

        $approvals = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('approvals.index', compact('approvals', 'isBusinessDivision', 'isManager'));
    }

    /**
     * 承認
     */
    public function approve(Request $request, WorkflowApproval $approval)
    {
        $user = Auth::user();
        
        // 承認権限チェック
        if ($approval->approver_id !== $user->id || $approval->status !== WorkflowApproval::STATUS_PENDING) {
            abort(403, 'この申請を承認する権限がありません。');
        }

        $validated = $request->validate([
            'comment' => 'nullable|string|max:1000',
        ]);

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

        // 申請者にメール通知
        Mail::to($approval->applicant->email)->send(new ApprovalNotification($approval));

        return redirect()->route('approvals.index')
            ->with('success', '承認しました。申請者に通知しました。');
    }

    /**
     * 差戻
     */
    public function reject(Request $request, WorkflowApproval $approval)
    {
        $user = Auth::user();
        
        // 承認権限チェック
        if ($approval->approver_id !== $user->id || $approval->status !== WorkflowApproval::STATUS_PENDING) {
            abort(403, 'この申請を差戻す権限がありません。');
        }

        $validated = $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

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

        // 申請者にメール通知
        Mail::to($approval->applicant->email)->send(new RejectionNotification($approval));

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
               $user->division_id === $businessDivision->children->pluck('id')->first();
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

