# mec-portal 設計書（Ver.2）

## 1. コンセプト

### システム概要
**mec-portal** は、日本メカトロン社内の情報・申請・資料を一元管理するポータルサイトです。  
「探す・申請する・共有する」をひとつの画面で完結させ、  
業務効率化と承認フローの透明化を目的とします。

### 想定利用者
- 一般社員（情報閲覧・申請）
- 部署責任者（出張・経費の承認）
- 経理担当（結算処理）
- 管理者（システム運用・お知らせ投稿）

### コア技術
| 項目 | 技術 |
|------|------|
| Framework | Laravel 12 + Blade |
| UI | Tailwind CSS + Alpine.js |
| DB | MySQL |
| Container | Laravel Sail (Docker) |
| PDF | barryvdh/laravel-dompdf |
| AI支援 | OpenAI API（文面要約・費目自動分類） |

---

## 2. 機能一覧

| 区分 | 機能名 | 概要 | 備考 |
|------|---------|------|------|
| 📊 共通 | ダッシュボード | お知らせ・申請状況・リンクを一覧表示 | ロール別表示 |
| 📰 情報共有 | お知らせ | 管理者が投稿／社員は閲覧 | AIで文面生成支援 |
| 📁 情報共有 | ドキュメント | 各種資料（PDF/Excel等）の共有・検索 | S3連携予定 |
| ❓ ナレッジ | FAQ | よくある質問をカテゴリ別に検索表示 | 管理者編集可能 |
| 📞 コミュニケーション | 問い合わせフォーム | 部署宛ての質問・要望送信 | 管理者が返信可能 |
| 🏢 業務支援 | 会議室予約 | 会議室の予約・キャンセル・一覧 | 重複予約防止ロジック |
| 💴 経費精算 | 経費申請フォーム | 入力→PDF生成→メール送信 | 経理直行ルート |
| 🚄 出張申請 | 出張・交通費精算 | 部署承認→経理承認 | 承認フロー必須 |
| 🔐 管理 | 承認フロー | 各申請のステータス・履歴管理 | WorkflowApprovalsで統一 |

---

## 3. 画面構成

```
ダッシュボード
 ├─ お知らせ一覧（最新5件）
 ├─ ドキュメント（最近追加）
 ├─ FAQ検索フォーム
 ├─ 申請ステータス（出張／経費）
 ├─ 会議室予約状況
 └─ 社内リンク集

お知らせ
 ├─ 一覧
 ├─ 詳細表示
 └─ 投稿（管理者）

ドキュメント
 ├─ 一覧
 ├─ 詳細（プレビュ）
 └─ アップロード（管理者）

経費精算
 ├─ 入力フォーム
 ├─ PDFプレビュ
 └─ メール送信

出張申請
 ├─ 入力フォーム
 ├─ 承認ステータス
 └─ PDF出力

会議室予約
 ├─ カレンダー表示
 ├─ 予約フォーム
 └─ 一覧・キャンセル

問い合わせ
 ├─ フォーム送信
 └─ 管理画面で返信スレッド表示

FAQ
 ├─ カテゴリ一覧
 └─ 検索結果
```

---

## 4. データモデル（ER 図）

```mermaid
erDiagram

    USERS ||--o{ USER_DIVISIONS : belongs
    DIVISIONS ||--o{ USER_DIVISIONS : includes
    DIVISIONS ||--o| USERS : managed_by

    USERS ||--o{ EXPENSES : submits
    USERS ||--o{ TRAVEL_REQUESTS : submits
    TRAVEL_REQUESTS ||--o{ TRAVEL_EXPENSES : includes
    USERS ||--o{ RESERVATIONS : books
    USERS ||--o{ INQUIRIES : sends
    USERS ||--o{ WORKFLOW_APPROVALS : approves

    EXPENSES ||--o| WORKFLOW_APPROVALS : approval_flow
    TRAVEL_EXPENSES ||--o| WORKFLOW_APPROVALS : approval_flow

    USERS {
        int id PK
        string user_code
        string name
        string mail
        tinyint role  // 0:user, 1:admin, 2:manager
        tinyint delete_flg
    }

    DIVISIONS {
        int id PK
        string name
        int manager_id FK
        int parent_id FK "null可"
    }

    USER_DIVISIONS {
        int id PK
        int user_id FK
        int division_id FK
        tinyint main_flag  // 1=主所属,0=兼任
        datetime created_at
    }

    EXPENSES {
        int id PK
        int user_id FK
        date date
        string category
        decimal amount
        string description
        string receipt_path
        tinyint status
    }

     TRAVEL_REQUESTS {
        int id PK
        int user_id FK
        string destination
        string purpose
        date departure_date
        date return_date
        decimal subtotal
        decimal advance_payment
        decimal settlement_amount
        tinyint settlement_type  // 0=返金,1=支給
        tinyint status  // 0=申請中,1=承認済,2=差戻
        datetime approved_at
        datetime created_at
    }

    TRAVEL_EXPENSES {
        int id PK
        int travel_request_id FK
        date date
        string description
        enum category('交通費','宿泊費','日当','半日当','その他')
        decimal cash
        decimal ticket
        decimal total
        text remarks
        datetime created_at
    }

    RESERVATIONS {
        int id PK
        int user_id FK
        string room_name
        datetime start_time
        datetime end_time
        string description
        tinyint status
    }

    INQUIRIES {
        int id PK
        int user_id FK
        string subject
        text message
        string department
        tinyint status
    }

    WORKFLOW_APPROVALS {
        int id PK
        string request_type
        int request_id
        int applicant_id FK
        int approver_id FK
        tinyint status
        text comment
        datetime approved_at
    }
```

---

## 5. 承認フロー概要

### 経費申請
```
社員（申請）
   ↓
経理（承認）
```

### 出張申請
```
出張申請フロー
社員（出張申請） → 部署責任者（承認） → 経理（最終承認）

承認対象：travel_requests.id
経費明細（travel_expenses）は参照のみ（更新不可）

```

### 会議室予約
```
社員（登録）
   ↓
即時反映（重複不可）
```

## 6. Excel生成・メール送信設計

| 機能 | テンプレート | 送信先 | 出力形式 | 添付ファイル名 |
|------|---------------|--------|-------------|----------------|
| 経費申請 | `resources/views/excel/expense_template.xlsx` | 経理部アドレス | Excel (`.xlsx`) | 経費申請書.xlsx |
| 出張申請 | `resources/views/excel/travel_template.xlsx` | 部署責任者・経理 | Excel (`.xlsx`) | 出張申請書.xlsx |
| 問い合わせ | - | 該当部署宛て | メール本文 | - |

### 出力形式の方針
- **Excel形式 (.xlsx)** を採用。
  - 承認印・コメント欄をセル内に追加可能。
  - システム生成後、担当者が追記・押印できるようにする。  
  - Excelは改ざん検知を有効化（変更履歴または保護付き）。
- Excel出力には `PhpSpreadsheet` を使用予定。

### 実装例
```php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Mail;

public function exportExpenseExcel($expense)
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setCellValue('A1', '経費申請書');
    $sheet->setCellValue('A3', '社員名');
    $sheet->setCellValue('B3', $expense->user->name);
    $sheet->setCellValue('A4', '日付');
    $sheet->setCellValue('B4', $expense->date);
    $sheet->setCellValue('A5', '金額');
    $sheet->setCellValue('B5', $expense->amount);
    $sheet->setCellValue('A6', '内容');
    $sheet->setCellValue('B6', $expense->description);

    $path = storage_path('app/public/expense_'.$expense->id.'.xlsx');
    $writer = new Xlsx($spreadsheet);
    $writer->save($path);

    Mail::to('keiri@mechatron.co.jp')
        ->send(new ExpenseSubmitted($expense, $path));
}
```

### セキュリティ対策
- ファイル保護（読み取り専用またはパスワード付与）
- 社内サーバーに保存後、自動削除ジョブを設定
- 改ざん検知用のハッシュ記録（ログに保存）

---

## 7. AI支援設計

| 対象 | AI活用例 | モデル |
|------|-----------|--------|
| お知らせ文 | 文体整形・要約・敬語調整 | GPT-4o-mini |
| 経費精算 | 内容から費目を自動分類 | GPT-4-turbo |
| FAQ | 質問文→既存回答検索 | Embedding |
| 出張目的 | 文面から自動要約して承認時表示 | GPT-4o-mini |

---

## 8. 開発ロードマップ（AI駆動モデル）

| フェーズ | 内容 | 期間 | 担当 |
|----------|------|------|------|
| ① 設計 | ER図・画面構成・要件整理 | 〜2025/11/10 | 美晴 |
| ② 自動生成 | AIでMigration／Model／Controller生成 | 〜2025/11/20 | ChatGPT＋美晴 |
| ③ UI構築 | Bladeテンプレート＋Tailwindレイアウト | 〜2025/12/01 | 美晴 |
| ④ 機能拡張 | 会議室／経費／出張／承認ワークフロー | 〜2026/01 | 美晴 |
| ⑤ AI連携 | GPT要約／費目分類／自動承認支援 | 〜2026/02 | 美晴 |

---

## 9. ディレクトリ構成（想定）

```
app/
 ├─ Models/
 │   ├─ User.php
 │   ├─ Division.php
 │   ├─ UserDivision.php
 │   ├─ Expense.php
 │   ├─ TravelExpense.php
 │   ├─ Reservation.php
 │   ├─ Inquiry.php
 │   └─ WorkflowApproval.php
 ├─ Http/Controllers/
 │   ├─ DashboardController.php
 │   ├─ NewsController.php
 │   ├─ ExpenseController.php
 │   ├─ TravelExpenseController.php
 │   ├─ ReservationController.php
 │   ├─ InquiryController.php
 │   └─ WorkflowController.php
resources/
 ├─ views/
 │   ├─ layouts/app.blade.php
 │   ├─ dashboard.blade.php
 │   ├─ news/
 │   ├─ documents/
 │   ├─ expenses/
 │   ├─ travel_expenses/
 │   ├─ reservations/
 │   ├─ inquiries/
 │   └─ excel/
 ├─ css/
 └─ js/
```

---

## 10. 今後の発展構想（Ver.3以降）

- ✅ SSO（社内AD連携）
- ✅ Teams／Slack承認通知
- ✅ 申請Excel自動保存（SharePoint or Drive連携）
- ✅ 出張申請と経費申請の自動紐付け
- ✅ ダッシュボードAI分析（承認率・利用率の可視化）

---

© 2025 Nihon Mechatron Co., Ltd.

