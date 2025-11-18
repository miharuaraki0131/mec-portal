# agent.md（AIコーディングガイドライン）

## 1. 目的
本ファイルは AI（Cursor / ChatGPT）が mec-portal の機能実装を行う際の共通ルールブックである。

---

## 2. 仕様書（plans/）の扱い
AI は以下の手順で処理する：

1. `docs/plans/` 配下の **対象 plan.md を単独で読み込む**
2. 仕様を正確に理解する
3. 必要なファイルを生成する：
   - Model
   - Controller
   - Migration
   - Factory/Seeder（必要な場合）
   - Request（任意）
   - Routes
   - Blade Views
4. 生成した内容を `docs/exec.md` に追記する

---

## 3. コーディングルール
- Framework: Laravel
- Layout: Breeze（blade）
- Controller: ResourceController を優先
- 命名規則：PSR-12 / Laravel code style
- 画面：Breeze の layout を継承する
- コメント：必要最小限でOK
- 日本語コメントは可

---

## 4. exec.md への追記形式

### ◆ テンプレート

```md
## YYYY-MM-DD (Name of Plan)

### 対応した仕様書
docs/plans/todo-plan.md

### 追加・更新したファイル
- app/Models/Todo.php
- app/Http/Controllers/TodoController.php
- database/migrations/xxxx_xx_xx_create_todos_table.php
- routes/web.php（todo の resource 追加）
- resources/views/todo/index.blade.php
- resources/views/todo/create.blade.php
- resources/views/todo/edit.blade.php

### 実装内容の要約
- ToDo 機能の CRUD を実装
- バリデーション追加
- Blade を Breeze に合わせて構成
- 一覧画面に完了/未完了表示
5. 注意事項

migration を変更する場合は新しい migration を作成する（書き換えない）

不明点は質問してから実装する

仕様にない機能は勝手に追加しない

Blade の UI はシンプルでOK


---

# ✅ ③ `docs/exec.md` のテンプレート（plan参照方式）

```md
# exec.md（AI 実装ログ）

このファイルは、AI が行った実装内容をすべて記録するためのログです。

---

## 2025-11-XX（todo-plan.md）

### 対応した仕様書
- docs/plans/todo-plan.md

### 追加・更新したファイル
（AIがここに列挙する）

### 実装内容
（AIが自動記述）

---

## 2025-11-XX（auth-plan.md 追加予定）
...