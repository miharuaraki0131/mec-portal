# agent.md（AIコーディングガイドライン）

## 1. 目的
このファイルは、AI（Cursor / ChatGPT）が mec-portal の機能実装を行う際に
「迷わず、統一された品質で」コード生成できるようにするための共通ルールブックである。

本ガイドラインは **AI駆動開発（AI-driven Development）** を前提とする。

---

## 2. 仕様書（docs/plans/）の扱い

AI は以下の手順で処理すること：

1. `docs/plans/` 配下の **対象 plan.md を単独で読み込む**
2. 仕様（Purpose + Minimal Requirements）を正確に理解する
3. 必要なファイルを生成する（下記）
   - Model
   - Controller（ResourceController を優先）
   - Migration
   - Factory / Seeder（必要な場合のみ）
   - FormRequest（必要なら AI 判断で作成可）
   - Routes（RESTful に追加）
   - Blade Views（Breeze レイアウトを継承）
4. 生成・更新した内容を **docs/exec.md に追記する**

---

## 3. コーディングルール（AIが従うべき基準）

### 🔧 フレームワーク
- Laravel

### 🎨 UI
- Breeze（Blade）をベースにする  
- 既存の mec-portal レイアウトに合わせる  
- 過度な装飾は不要、シンプルで可読性重視

### 📦 コード規約
- PSR-12  
- Laravel の慣習に従う（命名 / ディレクトリ構造 / Controller 設計）

### 📘 コメント
- 必要最小限の日本語コメントは許可
- コードの意図が曖昧な場合のみ補足コメントを入れる

### 🔐 認可 / セキュリティ
- Policy もしくは Gate を適切に利用して認可を実装
- 「自分のデータのみ閲覧・編集可」が原則

### 🗂 Migration
- 既存 migration を上書き禁止  
- 変更が必要な場合は **新しい migration を作成すること**

---

## 4. exec.md への追記形式（AIが必ず守る）

### ◆ テンプレート

```md
## YYYY-MM-DD（対応したplan名）

### 対応した仕様書
- docs/plans/todo-plan.md

### 追加・更新したファイル
- app/Models/Todo.php
- app/Http/Controllers/TodoController.php
- database/migrations/2025_xx_xx_create_todos_table.php
- routes/web.php
- resources/views/todo/index.blade.php
- resources/views/todo/create.blade.php
- resources/views/todo/edit.blade.php

### 実装内容の要約
- ToDo 機能の CRUD を実装
- 認可により「自分のタスクのみ操作可」を追加
- バリデーション追加
- Breeze レイアウトで Blade を構成
- 完了/未完了の切替を実装（JS）
```

---

## 5. 注意事項

- migration の書き換え禁止（必ず追加方式）
- 不明点がある場合、質問してから実装すること
- 仕様にない機能を勝手に追加しない
- UI はシンプルかつ既存ポータルに馴染む形にする
- exec.md への記録漏れ禁止

---

