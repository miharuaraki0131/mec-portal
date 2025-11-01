# テストファイル説明

このディレクトリには、mec-portalアプリケーションのテストファイルが含まれています。

## テストファイル一覧

### Feature テスト

#### ExpenseTest.php
経費申請機能のテストです。以下の機能をテストします：

- `test_user_can_create_expense`: ユーザーが経費申請を作成できることをテスト
- `test_expense_approval_flow_for_business_division`: 業務部を選択した場合の承認フローテスト
- `test_expense_approval_flow_for_division_manager`: 部門管理者を選択した場合の承認フローテスト
- `test_business_division_user_can_approve_expense`: 業務部のユーザーが承認できることをテスト
- `test_system_admin_cannot_approve_expense`: システム管理者が承認できないことをテスト
- `test_can_get_pending_approval_count`: 承認待ち件数を取得できることをテスト

#### ApprovalTest.php
承認機能のテストです。以下の機能をテストします：

- `test_business_division_user_can_view_approvals`: 業務部のユーザーが承認待ち一覧を閲覧できることをテスト
- `test_system_admin_cannot_access_approvals`: システム管理者が承認待ち一覧にアクセスできないことをテスト
- `test_division_manager_can_approve_expense`: 部門管理者が承認できることをテスト
- `test_can_reject_expense`: 差戻ができることをテスト
- `test_travel_request_two_stage_approval_flow`: 出張申請の2段階承認フローをテスト
- `test_deleted_expense_not_shown_in_approvals`: 削除された申請が承認待ち一覧に表示されないことをテスト

## テストの実行方法

### 全テストを実行
```bash
./vendor/bin/sail php artisan test
```

### 特定のテストスイートを実行
```bash
# Featureテストのみ
./vendor/bin/sail php artisan test --testsuite=Feature

# Unitテストのみ
./vendor/bin/sail php artisan test --testsuite=Unit
```

### 特定のテストクラスを実行
```bash
./vendor/bin/sail php artisan test --filter=ExpenseTest
./vendor/bin/sail php artisan test --filter=ApprovalTest
```

### 特定のテストメソッドを実行
```bash
./vendor/bin/sail php artisan test --filter=test_user_can_create_expense
```

### PHPUnitを直接使用
```bash
./vendor/bin/sail php vendor/bin/phpunit
```

## テスト環境の設定

テストは`phpunit.xml`の設定に基づいて実行されます。テスト用のデータベースは自動的に作成・削除されます（`RefreshDatabase`トレイトを使用）。

### 環境変数
- `APP_ENV=testing`
- `DB_DATABASE=testing`
- `MAIL_MAILER=array` (メールは実際に送信されません)

## 注意事項

- テストでは実際のメールは送信されません（`Mail::fake()`を使用）
- テスト用のデータベースは各テスト実行前に自動的にリフレッシュされます
- テスト用のユーザーファクトリは`UserFactory`を使用します

## テストの追加

新しいテストを追加する場合は、以下の構造に従ってください：

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;

class YourTest extends TestCase
{
    public function test_something(): void
    {
        // テストの実装
    }
}
```

## トラブルシューティング

### マイグレーションエラーが発生する場合
テスト用データベースを再作成：
```bash
./vendor/bin/sail php artisan migrate:fresh --env=testing
```

### テストが失敗する場合
- データベースの状態を確認
- マイグレーションの順序を確認
- テスト用のデータが正しく作成されているか確認

