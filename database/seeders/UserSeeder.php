<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 管理者アカウント（role: 1）
        User::create([
            'user_code' => 'ADMIN001',
            'name' => '管理者',
            'email' => 'admin@mechatron.co.jp',
            'password' => Hash::make('password'),
            'role' => 1, // admin
            'delete_flg' => 0,
            'email_verified_at' => now(),
        ]);

        // 部署責任者（role: 2）
        User::create([
            'user_code' => 'MGR001',
            'name' => '部署責任者',
            'email' => 'manager@mechatron.co.jp',
            'password' => Hash::make('password'),
            'role' => 2, // manager
            'delete_flg' => 0,
            'email_verified_at' => now(),
        ]);

        // 一般ユーザー（role: 0）
        User::create([
            'user_code' => 'USER001',
            'name' => '山田 太郎',
            'email' => 'yamada@mechatron.co.jp',
            'password' => Hash::make('password'),
            'role' => 0, // user
            'delete_flg' => 0,
            'email_verified_at' => now(),
        ]);

        User::create([
            'user_code' => 'USER002',
            'name' => '佐藤 花子',
            'email' => 'sato@mechatron.co.jp',
            'password' => Hash::make('password'),
            'role' => 0, // user
            'delete_flg' => 0,
            'email_verified_at' => now(),
        ]);

        User::create([
            'user_code' => 'USER003',
            'name' => '鈴木 一郎',
            'email' => 'suzuki@mechatron.co.jp',
            'password' => Hash::make('password'),
            'role' => 0, // user
            'delete_flg' => 0,
            'email_verified_at' => now(),
        ]);

        $this->command->info('ユーザーのシーディングが完了しました。');
        $this->command->info('管理者: admin@mechatron.co.jp / password');
        $this->command->info('部署責任者: manager@mechatron.co.jp / password');
        $this->command->info('一般ユーザー: yamada@mechatron.co.jp / password');
    }
}

