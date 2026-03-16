# Requirements Specification

## 1. 概要
社内ポータルサイトに「AIミーティング議事録機能」を実装する。
ブラウザで録音した会議音声をサーバーへアップロードし、Google Gemini 1.5 Flash APIを用いて「文字起こし」と「要約」を自動生成する。

## 2. データモデル設計 (Database)

### 2.1 Meetings Table
会議情報を管理するテーブル。

| カラム名 | 論理名 | 型 | 制約/デフォルト | 説明 |
| :--- | :--- | :--- | :--- | :--- |
| `id` | ID | BigInteger | PK, Auto Increment | |
| `title` | 議題 | String | Nullable | 会議のタイトル |
| `participants` | 参加者 | Text | Nullable | 参加者名（単一テキストフィールドとして保存） |
| `held_at` | 開催日時 | DateTime | Nullable | 会議の行われた日時 |
| `audio_path` | 音声パス | String | Nullable | `storage/app/private` 内のパス |
| `transcript` | 文字起こし | LongText | Nullable | AI生成された全文 |
| `summary` | 要約 | LongText | Nullable | AI生成された要約 |
| `status` | ステータス | String | Default: `pending` | 処理状態 (Enum管理) |
| `created_at` | 作成日時 | Timestamp | | |
| `updated_at` | 更新日時 | Timestamp | | |

### 2.2 Status Definition (Enum)
ステータスは PHPの `MeetingStatus` Enumとして定義し、Modelでキャストする。

- `pending`: 録音準備中・録音中
- `processing`: 音声アップロード済み・AI処理中
- `completed`: 処理完了（閲覧可能）
- `failed`: エラー発生

## 3. 機能要件 (Backend)

### 3.1 会議作成 API
- **Endpoint**: `POST /api/meetings`
- **処理概要**:
  1. リクエストバリデーションを行う。
  2. 音声ファイルを `private` ディスクの `meetings/{YYYY-MM-DD}` ディレクトリに保存する。
  3. `meetings` テーブルにレコードを作成する（Status: `processing`）。
  4. 非同期ジョブ `ProcessMeetingAudio` をディスパッチする。
  5. 作成された `meeting` オブジェクトをJSONで返す。

### 3.2 AI処理ジョブ (ProcessMeetingAudio)
- **概要**: Gemini APIと通信し、議事録を生成する。
- **フロー**:
  1. 対象の音声ファイルを取得。
  2. Gemini File API へアップロード。
  3. Gemini 1.5 Flash へプロンプト送信（JSONモード）。
     - プロンプトには `participants`（参加者名）を含め、話者特定精度を上げる。
  4. レスポンス（`transcript`, `summary`）をDBに保存し、Statusを `completed` に更新。
  5. Gemini上のファイルを削除する（クリーンアップ）。

## 4. 非機能要件
- **Laravel Version**: 12.x
- **PHP Version**: 8.2+
- **Security**: 音声ファイルは公開ディレクトリに置かず、Download用のアクション経由でのみアクセス可能にする。