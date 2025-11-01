<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Document;
use App\Models\WorkflowApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // キャッシュキー（5分間有効）
        $cacheKey = 'dashboard_data_' . $user->id;
        
        // キャッシュから取得、なければデータベースから取得してキャッシュに保存
        $data = Cache::remember($cacheKey, 300, function () use ($user) {
            // 最新のお知らせを5件取得
            $latestNews = News::with('postedBy')
                ->published()
                ->priority()
                ->latest('published_at')
                ->limit(5)
                ->get();

            // 最近アップロードされた資料を5件取得
            $recentDocuments = Document::with('uploadedBy')
                ->latest()
                ->limit(5)
                ->get();

            // 承認待ち件数を取得
            $isBusinessDivision = $this->isBusinessDivisionMember($user);
            $isDivisionManager = $this->isDivisionManager($user);
            // システム管理者は承認権限なし
            $hasApprovalPermission = ($isBusinessDivision || $isDivisionManager) && $user->role !== 1;
            $pendingApprovalCount = $hasApprovalPermission ? $this->getPendingApprovalCount($user) : 0;

            return [
                'latestNews' => $latestNews,
                'recentDocuments' => $recentDocuments,
                'pendingApprovalCount' => $pendingApprovalCount,
                'hasApprovalPermission' => $hasApprovalPermission,
            ];
        });

        return view('dashboard', [
            'latestNews' => $data['latestNews'],
            'recentDocuments' => $data['recentDocuments'],
            'pendingApprovalCount' => $data['pendingApprovalCount'],
            'hasApprovalPermission' => $data['hasApprovalPermission'],
        ]);
    }

    /**
     * 承認待ち件数を取得（承認権限があることが前提）
     */
    private function getPendingApprovalCount($user)
    {
        // システム管理者は承認権限なし
        if ($user->role === 1) {
            return 0;
        }

        $isBusinessDivision = $this->isBusinessDivisionMember($user);
        $isDivisionManager = $this->isDivisionManager($user);

        // 権限チェック（念のため）
        if (!$isBusinessDivision && !$isDivisionManager) {
            return 0;
        }

        $query = WorkflowApproval::pending();
        
        // 必ず条件を追加（空のwhereだと全件マッチしてしまうため）
        $query->where(function ($q) use ($user, $isBusinessDivision, $isDivisionManager) {
            $hasCondition = false;
            
            if ($isBusinessDivision) {
                $hasCondition = true;
                $q->where(function ($subQ) use ($user) {
                    $subQ->where('request_type', 'expense')
                        ->where('approver_id', $user->id)
                        ->whereExists(function ($existsQuery) {
                            $existsQuery->selectRaw(1)
                                ->from('expenses')
                                ->whereColumn('expenses.id', 'workflow_approvals.request_id')
                                ->whereNull('expenses.parent_id')
                                ->where('expenses.approver_type', 'business');
                        });
                    
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
                $managedDivisionIds = \App\Models\Division::where('manager_id', $user->id)->pluck('id');
                
                if ($hasCondition) {
                    $q->orWhere(function ($subQ) use ($user, $managedDivisionIds) {
                        $subQ->where('request_type', 'expense')
                            ->where('approver_id', $user->id)
                            ->whereExists(function ($existsQuery) use ($user) {
                                $existsQuery->selectRaw(1)
                                    ->from('expenses')
                                    ->whereColumn('expenses.id', 'workflow_approvals.request_id')
                                    ->whereNull('expenses.parent_id')
                                    ->where('expenses.approver_type', 'manager');
                            });
                        
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
                } else {
                    $hasCondition = true;
                    $q->where(function ($subQ) use ($user, $managedDivisionIds) {
                        $subQ->where('request_type', 'expense')
                            ->where('approver_id', $user->id)
                            ->whereExists(function ($existsQuery) use ($user) {
                                $existsQuery->selectRaw(1)
                                    ->from('expenses')
                                    ->whereColumn('expenses.id', 'workflow_approvals.request_id')
                                    ->whereNull('expenses.parent_id')
                                    ->where('expenses.approver_type', 'manager');
                            });
                        
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
            }
            
            // 条件がない場合は絶対にマッチしない条件を追加
            if (!$hasCondition) {
                $q->whereRaw('1 = 0');
            }
        });
        
        return $query->count();
    }

    /**
     * 業務部のメンバーかどうか
     */
    private function isBusinessDivisionMember($user)
    {
        if (!$user->division) {
            return false;
        }

        // ApprovalControllerと同じロジックを使用
        $businessDivision = \App\Models\Division::where('name', '業務部')->whereNull('parent_id')->first();
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
    private function isDivisionManager($user): bool
    {
        return \App\Models\Division::where('manager_id', $user->id)->exists();
    }
}

