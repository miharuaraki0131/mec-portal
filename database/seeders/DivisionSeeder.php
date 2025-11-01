<?php

namespace Database\Seeders;

use App\Models\Division;
use Illuminate\Database\Seeder;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 親部署（事業部）
        $businessDivision = Division::create([
            'name' => '業務部',
        ]);

        $improvementDivision = Division::create([
            'name' => '業務改善部',
        ]);

        $firstDivision = Division::create([
            'name' => '第1事業部',
        ]);

        $secondDivision = Division::create([
            'name' => '第2事業部',
        ]);

        // 業務部の課
        Division::create([
            'name' => '業務課',
            'parent_id' => $businessDivision->id,
        ]);

        // 業務改善部の課（組織図では課なし）

        // 第1事業部の課
        Division::create([
            'name' => '製品開発課',
            'parent_id' => $firstDivision->id,
        ]);

        Division::create([
            'name' => '受託開発課',
            'parent_id' => $firstDivision->id,
        ]);

        // 第2事業部の課
        Division::create([
            'name' => '計測制御課',
            'parent_id' => $secondDivision->id,
        ]);

        Division::create([
            'name' => '技術支援課',
            'parent_id' => $secondDivision->id,
        ]);

        $this->command->info('部署のシーディングが完了しました。');
    }
}
