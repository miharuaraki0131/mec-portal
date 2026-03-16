# Implementation Plan

## 現在の実装フェーズ: Phase 1 (MVP実装)

- [ ] **Step 1: DB & Model 設計**
  - Migration作成: `meetings` テーブル (title, participants, audio_path, transcript, summary, status)
  - Model作成: `Meeting` クラスとFillable設定、Status Enum定義。

- [ ] **Step 2: Backend API 実装**
  - Controller作成: `MeetingController::store`
  - Request Validation作成: 音声ファイルのサイズ制限などを定義。
  - Route定義: `routes/api.php`

- [ ] **Step 3: 非同期Job 実装**
  - Job作成: `ProcessMeetingAudio`
  - Gemini API クライアントの実装 (Http Facade利用)
  - プロンプトの設計とJSONパース処理の実装
  - クリーンアップ処理 (Geminiファイル削除)

- [ ] **Step 4: Frontend (録音機能) 実装**
  - Blade View作成
  - JavaScript実装: `MediaRecorder` APIの制御
  - JavaScript実装: `IndexedDB` への安全な一時保存ロジック
  - API送信処理 (FormData)

- [ ] **Step 5: インフラ設定調整**
  - `php.ini`: `upload_max_filesize` 等の緩和
  - Docker再起動と設定反映確認

## 影響範囲
- `routes/web.php` または `routes/api.php`
- `storage/` ディレクトリの権限
- `.env` (GEMINI_API_KEYの追加)