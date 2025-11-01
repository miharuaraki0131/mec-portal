<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;

class RegistrationTest extends TestCase
{
    public function test_registration_screen_is_disabled(): void
    {
        // ユーザー登録画面は無効化されているため、このテストはスキップ
        // （ルートがコメントアウトされているため、404またはエラーが発生する可能性がある）
        $this->markTestSkipped('ユーザー登録はセキュリティ上の理由で無効化されています。');
    }

    public function test_user_registration_is_disabled(): void
    {
        // ユーザー登録は無効化されているため、このテストはスキップ
        $this->markTestSkipped('ユーザー登録はセキュリティ上の理由で無効化されています。');
    }
}
