# ToDo 機能 仕様書（todo-plan.md）

## 1. 概要
本仕様書は「個人向けToDo管理機能」の仕様を定義する。  
mec-portal 内の1機能として開発し、AI（Cursor/ChatGPT）が本仕様を参照して実装を行う。

---

## 2. 機能目的
- シンプルな個人ToDo管理を提供する  
- ステータス管理（未完了/完了）による日次業務の可視化  
- 将来的にダッシュボードや通知機能と連携できる構造を持つ

---

## 3. 画面仕様

### ◆ 一覧 `/todo`
- タスク一覧（テーブル表示）
- 新規追加ボタン
- 編集ボタン
- 削除ボタン
- 完了 / 未完了 表示
- 完了チェックボタン（クリックで即反映）
- 並び替え（id DESC でOK）

---

## 4. 新規作成 `/todo/create`
入力項目：
- title（必須）
- description（任意）
- due_date（任意）

---

## 5. 編集 `/todo/{id}/edit`
- 既存データをフォームに表示
- 更新ボタン

---

## 6. モデル仕様（ToDo）

| 項目 | 型 | 必須 | 説明 |
|------|------|------|------|
| id | big integer | ○ | PK |
| title | string(100) | ○ | タスク名 |
| description | text | - | 詳細 |
| status | tinyint | ○ | 0=未完了 / 1=完了 |
| due_date | date | - | 期限日 |
| created_at | timestamp | ○ | 自動 |
| updated_at | timestamp | ○ | 自動 |

---

## 7. バリデーション
| 項目 | ルール |
|------|-------|
| title | required / max:100 |
| description | nullable |
| status | boolean |
| due_date | nullable / date |

---

## 8. ルーティング
Resource Controller を利用：

GET /todo
GET /todo/create
POST /todo
GET /todo/{id}/edit
PUT /todo/{id}
DELETE /todo/{id}


## 9. Migration 仕様
- id: bigIncrements
- title: string(100)
- description: text nullable
- status: tinyInteger default 0
- due_date: date nullable
- timestamps

---

## 10. AIへの依頼方法
Cursor または ChatGPT へ次のように依頼する：

この仕様書（docs/plans/todo-plan.md）をもとに  、mec-portal に「個人ToDo（My ToDo）」機能を追加してください。
仕様は意図（Purpose）と必須要件（Minimal Requirements）のみを定義しており、
画面構成・DBスキーマ・バリデーション・ルーティング・UIの詳細は
AI の判断で最適なものを設計してください。

必要な実装は以下：
- Migration（todos テーブル）
- Model（Todo）
- Controller（TodoController）
- Routes
- Blade テンプレート（一覧 / 作成 / 編集）
- 認可（自分のみ閲覧/操作可能）
- ダッシュボードのカード追加
- サイドバーにメニュー追加

実装した内容とファイルパス・編集箇所は docs/exec.md に追記してください。

---

