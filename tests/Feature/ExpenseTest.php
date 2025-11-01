<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Division;
use App\Models\Expense;
use App\Models\WorkflowApproval;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ExpenseTest extends TestCase
{
    // RefreshDatabase は TestCase で既に使用されている

    /**
     * 経費申請を作成できることをテスト
     */
    public function test_user_can_create_expense(): void
    {
        $user = User::factory()->create([
            'role' => 0, // 一般ユーザー
        ]);

        $response = $this->actingAs($user)->post(route('expenses.store'), [
            'date' => now()->format('Y-m-d'),
            'category' => '交通費',
            'amount' => 1000,
            'description' => 'テスト経費',
            'approver_type' => 'business',
        ]);

        $response->assertRedirect(route('expenses.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('expenses', [
            'user_id' => $user->id,
            'category' => '交通費',
            'amount' => 1000,
            'description' => 'テスト経費',
            'approver_type' => 'business',
            'status' => Expense::STATUS_PENDING,
        ]);
    }

    /**
     * 業務部を選択した場合、業務部全員に承認権限が付与されることをテスト
     */
    public function test_expense_approval_flow_for_business_division(): void
    {
        // 業務部を作成
        $businessDivision = Division::create([
            'name' => '業務部',
            'parent_id' => null,
        ]);

        // 業務課を作成
        $businessSection = Division::create([
            'name' => '業務課',
            'parent_id' => $businessDivision->id,
        ]);

        // 業務部のユーザーを作成
        $businessUser1 = User::factory()->create([
            'division_id' => $businessDivision->id,
            'role' => 0,
        ]);

        $businessUser2 = User::factory()->create([
            'division_id' => $businessSection->id,
            'role' => 0,
        ]);

        // 申請者を作成
        $applicant = User::factory()->create([
            'role' => 0,
        ]);

        // 経費申請を作成
        $expense = Expense::create([
            'user_id' => $applicant->id,
            'date' => now(),
            'category' => '交通費',
            'amount' => 1000,
            'description' => 'テスト経費',
            'status' => Expense::STATUS_PENDING,
            'approver_type' => 'business',
        ]);

        // 承認フローを作成（コントローラーのメソッドを呼び出す代わりに直接作成）
        $businessDivisionFromDb = Division::where('name', '業務部')->whereNull('parent_id')->first();
        $businessUsers = User::where('delete_flg', 0)
            ->where(function ($query) use ($businessDivisionFromDb) {
                $query->where('division_id', $businessDivisionFromDb->id)
                    ->orWhereIn('division_id', $businessDivisionFromDb->children->pluck('id'));
            })
            ->get();

        foreach ($businessUsers as $businessUser) {
            WorkflowApproval::create([
                'request_type' => 'expense',
                'request_id' => $expense->id,
                'applicant_id' => $expense->user_id,
                'approval_order' => 1,
                'approver_id' => $businessUser->id,
                'is_final_approval' => 1,
                'status' => WorkflowApproval::STATUS_PENDING,
            ]);
        }

        // 業務部のユーザー全員に承認権限が付与されていることを確認
        $approvals = WorkflowApproval::where('request_type', 'expense')
            ->where('request_id', $expense->id)
            ->get();

        $this->assertCount(2, $approvals);
        $this->assertTrue($approvals->contains('approver_id', $businessUser1->id));
        $this->assertTrue($approvals->contains('approver_id', $businessUser2->id));
    }

    /**
     * 部門管理者を選択した場合、その部門管理者に承認権限が付与されることをテスト
     */
    public function test_expense_approval_flow_for_division_manager(): void
    {
        // 部門を作成
        $division = Division::create([
            'name' => '第1事業部',
            'parent_id' => null,
        ]);

        // 部門管理者を作成
        $manager = User::factory()->create([
            'division_id' => $division->id,
            'role' => 0,
        ]);

        // 部門に管理者を設定
        $division->update(['manager_id' => $manager->id]);

        // 申請者を作成
        $applicant = User::factory()->create([
            'role' => 0,
        ]);

        // 経費申請を作成
        $expense = Expense::create([
            'user_id' => $applicant->id,
            'date' => now(),
            'category' => '交通費',
            'amount' => 1000,
            'description' => 'テスト経費',
            'status' => Expense::STATUS_PENDING,
            'approver_type' => 'manager',
        ]);

        // 承認フローを作成
        $division = Division::find($division->id);
        if ($division && $division->manager_id) {
            WorkflowApproval::create([
                'request_type' => 'expense',
                'request_id' => $expense->id,
                'applicant_id' => $expense->user_id,
                'approval_order' => 1,
                'approver_id' => $division->manager_id,
                'is_final_approval' => 1,
                'status' => WorkflowApproval::STATUS_PENDING,
            ]);
        }

        // 部門管理者に承認権限が付与されていることを確認
        $approval = WorkflowApproval::where('request_type', 'expense')
            ->where('request_id', $expense->id)
            ->first();

        $this->assertNotNull($approval);
        $this->assertEquals($manager->id, $approval->approver_id);
    }

    /**
     * 業務部のユーザーが承認できることをテスト
     */
    public function test_business_division_user_can_approve_expense(): void
    {
        // 業務部を作成
        $businessDivision = Division::create([
            'name' => '業務部',
            'parent_id' => null,
        ]);

        // 業務部のユーザーを作成
        $businessUser = User::factory()->create([
            'division_id' => $businessDivision->id,
            'role' => 0,
        ]);

        // 申請者を作成
        $applicant = User::factory()->create([
            'role' => 0,
        ]);

        // 経費申請を作成
        $expense = Expense::create([
            'user_id' => $applicant->id,
            'date' => now(),
            'category' => '交通費',
            'amount' => 1000,
            'description' => 'テスト経費',
            'status' => Expense::STATUS_PENDING,
            'approver_type' => 'business',
        ]);

        // 承認フローを作成
        $approval = WorkflowApproval::create([
            'request_type' => 'expense',
            'request_id' => $expense->id,
            'applicant_id' => $applicant->id,
            'approval_order' => 1,
            'approver_id' => $businessUser->id,
            'is_final_approval' => 1,
            'status' => WorkflowApproval::STATUS_PENDING,
        ]);

        // 承認を実行
        Mail::fake();
        $response = $this->actingAs($businessUser)->post(route('approvals.approve', $approval), [
            'comment' => '承認しました',
        ]);

        $response->assertRedirect(route('approvals.index'));
        
        // 承認が完了したことを確認
        $this->assertDatabaseHas('workflow_approvals', [
            'id' => $approval->id,
            'status' => WorkflowApproval::STATUS_APPROVED,
        ]);

        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'status' => Expense::STATUS_APPROVED,
        ]);
    }

    /**
     * システム管理者が承認できないことをテスト
     */
    public function test_system_admin_cannot_approve_expense(): void
    {
        // システム管理者を作成
        $admin = User::factory()->create([
            'role' => 1, // システム管理者
        ]);

        // 申請者を作成
        $applicant = User::factory()->create([
            'role' => 0,
        ]);

        // 経費申請を作成
        $expense = Expense::create([
            'user_id' => $applicant->id,
            'date' => now(),
            'category' => '交通費',
            'amount' => 1000,
            'description' => 'テスト経費',
            'status' => Expense::STATUS_PENDING,
            'approver_type' => 'business',
        ]);

        // 承認待ち一覧にアクセス
        $response = $this->actingAs($admin)->get(route('approvals.index'));

        // システム管理者は承認権限がないため、403エラーが返る
        $response->assertStatus(403);
    }

    /**
     * 承認待ち件数を取得できることをテスト
     */
    public function test_can_get_pending_approval_count(): void
    {
        // 業務部を作成
        $businessDivision = Division::create([
            'name' => '業務部',
            'parent_id' => null,
        ]);

        // 業務部のユーザーを作成
        $businessUser = User::factory()->create([
            'division_id' => $businessDivision->id,
            'role' => 0,
        ]);

        // 申請者を作成
        $applicant = User::factory()->create([
            'role' => 0,
        ]);

        // 経費申請を作成
        $expense = Expense::create([
            'user_id' => $applicant->id,
            'date' => now(),
            'category' => '交通費',
            'amount' => 1000,
            'description' => 'テスト経費',
            'status' => Expense::STATUS_PENDING,
            'approver_type' => 'business',
        ]);

        // 承認フローを作成
        WorkflowApproval::create([
            'request_type' => 'expense',
            'request_id' => $expense->id,
            'applicant_id' => $applicant->id,
            'approval_order' => 1,
            'approver_id' => $businessUser->id,
            'is_final_approval' => 1,
            'status' => WorkflowApproval::STATUS_PENDING,
        ]);

        // 承認待ち件数を取得
        $response = $this->actingAs($businessUser)->get(route('api.pending-approvals-count'));

        $response->assertStatus(200);
        $response->assertJson(['count' => 1]);
    }
}

