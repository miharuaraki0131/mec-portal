<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Division;
use App\Models\Expense;
use App\Models\TravelRequest;
use App\Models\WorkflowApproval;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ApprovalTest extends TestCase
{
    // RefreshDatabase は TestCase で既に使用されている

    /**
     * 業務部のユーザーが承認待ち一覧を閲覧できることをテスト
     */
    public function test_business_division_user_can_view_approvals(): void
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

        // 承認待ち一覧にアクセス
        $response = $this->actingAs($businessUser)->get(route('approvals.index'));

        $response->assertStatus(200);
        $response->assertSee('承認待ち一覧');
    }

    /**
     * システム管理者が承認待ち一覧にアクセスできないことをテスト
     */
    public function test_system_admin_cannot_access_approvals(): void
    {
        // システム管理者を作成
        $admin = User::factory()->create([
            'role' => 1, // システム管理者
        ]);

        // 承認待ち一覧にアクセス
        $response = $this->actingAs($admin)->get(route('approvals.index'));

        $response->assertStatus(403);
    }

    /**
     * 部門管理者が承認できることをテスト
     */
    public function test_division_manager_can_approve_expense(): void
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
        $approval = WorkflowApproval::create([
            'request_type' => 'expense',
            'request_id' => $expense->id,
            'applicant_id' => $applicant->id,
            'approval_order' => 1,
            'approver_id' => $manager->id,
            'is_final_approval' => 1,
            'status' => WorkflowApproval::STATUS_PENDING,
        ]);

        // 承認を実行
        Mail::fake();
        $response = $this->actingAs($manager)->post(route('approvals.approve', $approval), [
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
     * 差戻ができることをテスト
     */
    public function test_can_reject_expense(): void
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

        // 差戻を実行
        Mail::fake();
        $response = $this->actingAs($businessUser)->post(route('approvals.reject', $approval), [
            'comment' => '内容を確認してください',
        ]);

        $response->assertRedirect(route('approvals.index'));
        
        // 差戻が完了したことを確認
        $this->assertDatabaseHas('workflow_approvals', [
            'id' => $approval->id,
            'status' => WorkflowApproval::STATUS_REJECTED,
        ]);

        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'status' => Expense::STATUS_REJECTED,
        ]);
    }

    /**
     * 出張申請の2段階承認フローをテスト
     */
    public function test_travel_request_two_stage_approval_flow(): void
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

        // 申請者を作成（部門に所属）
        $applicant = User::factory()->create([
            'division_id' => $division->id,
            'role' => 0,
        ]);

        // 出張申請を作成
        $travelRequest = TravelRequest::create([
            'user_id' => $applicant->id,
            'destination' => '東京',
            'purpose' => '会議',
            'departure_date' => now()->addDays(7),
            'return_date' => now()->addDays(9),
            'advance_payment' => 50000,
            'status' => TravelRequest::STATUS_PENDING,
        ]);

        // 第1段階：部門管理者の承認フローを作成
        $firstApproval = WorkflowApproval::create([
            'request_type' => 'travel',
            'request_id' => $travelRequest->id,
            'applicant_id' => $applicant->id,
            'approval_order' => 1,
            'approver_id' => $manager->id,
            'is_final_approval' => 0,
            'status' => WorkflowApproval::STATUS_PENDING,
        ]);

        // 部門管理者が承認
        Mail::fake();
        $response = $this->actingAs($manager)->post(route('approvals.approve', $firstApproval), [
            'comment' => '承認しました',
        ]);

        $response->assertRedirect(route('approvals.index'));

        // 第2段階：業務部の承認フローが作成されていることを確認
        $secondApproval = WorkflowApproval::where('request_type', 'travel')
            ->where('request_id', $travelRequest->id)
            ->where('approval_order', 2)
            ->where('is_final_approval', 1)
            ->first();

        $this->assertNotNull($secondApproval);
        $this->assertEquals($businessUser->id, $secondApproval->approver_id);
        $this->assertEquals(WorkflowApproval::STATUS_PENDING, $secondApproval->status);

        // 業務部のユーザーが最終承認
        $response = $this->actingAs($businessUser)->post(route('approvals.approve', $secondApproval), [
            'comment' => '最終承認しました',
        ]);

        $response->assertRedirect(route('approvals.index'));

        // 出張申請が承認済みになったことを確認
        $this->assertDatabaseHas('travel_requests', [
            'id' => $travelRequest->id,
            'status' => TravelRequest::STATUS_APPROVED,
        ]);
    }

    /**
     * 削除された申請が承認待ち一覧に表示されないことをテスト
     */
    public function test_deleted_expense_not_shown_in_approvals(): void
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

        // 申請を削除
        $expense->delete();

        // 承認待ち件数を取得
        $response = $this->actingAs($businessUser)->get(route('api.pending-approvals-count'));

        $response->assertStatus(200);
        $response->assertJson(['count' => 0]);
    }
}

