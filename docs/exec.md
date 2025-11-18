# exec.md（AI 実装ログ / Implementation Log）

本ファイルは、AI（Cursor / ChatGPT）が行った実装内容を時系列で記録するためのログです。  
各エントリは「どの仕様書（plan）をもとに」「どのファイルを」「どう変更したか」が分かる形式で記述します。

---

## 📌 書き方ルール
- 日付順に追記（上に追加でも下に追加でもOK）
- 必ず **どの plan を参照したか** を明示する
- 追加したファイル・更新したファイルを一覧で列挙
- 変更内容を簡潔にまとめる（詳細は PR や diff に任せる）
- コマンド実行ログは必要な場合だけ記載

---

# -----------------------------
# ▼ ここより下に AI が追記する
# -----------------------------

---

## 2025-11-18（todo-plan.md）

### 対応した仕様書
- `docs/plans/todo-plan.md`

### 追加・更新したファイル
**新規追加**
- `app/Models/Todo.php`
- `app/Policies/TodoPolicy.php`
- `app/Http/Controllers/TodoController.php`
- `database/migrations/2025_11_18_122108_create_todos_table.php`
- `resources/views/todo/index.blade.php`
- `resources/views/todo/create.blade.php`
- `resources/views/todo/edit.blade.php`

**更新**
- `routes/web.php`（todo の resource route と toggle-status route 追加）
- `app/Http/Controllers/DashboardController.php`（ToDo統計情報の取得を追加）
- `resources/views/dashboard.blade.php`（My ToDoカードを追加）
- `resources/views/layouts/navigation.blade.php`（サイドバーにMy ToDoメニューを追加）

### 実装内容（要約）
- ToDo（個人タスク）管理機能の CRUD を実装
- Migration: todos テーブル（user_id, title, description, status, due_date）
- Model: Todo モデル（ステータス定数、スコープ、リレーション）
- Policy: TodoPolicy（自分のToDoのみ閲覧/操作可能）
- Controller: TodoController（index, create, store, edit, update, destroy, toggleStatus）
- Routes: resource route と toggle-status route を追加
- Blade テンプレート: 一覧（テーブル表示、完了チェックボタン）、作成、編集画面
- バリデーション: title（required, max:100）、description（nullable）、due_date（nullable, date）、status（boolean）
- 認可: 自分のToDoのみ閲覧・編集・削除可能
- ダッシュボード: My ToDoカードを追加（未完了件数を表示）
- サイドバー: My ToDoメニューを追加（デスクトップ・モバイル対応）  

---

## 2025-11-XX（auth-plan.md）※例
### 対応した仕様書
- `docs/plans/auth-plan.md`

### 追加・更新したファイル
…

### 実装内容（要約）
…

---

# ※以後、AI が同じフォーマットで追記していく
