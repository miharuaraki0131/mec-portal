<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CleanOldFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'files:clean-old 
                            {--days=90 : 削除する古いファイルの日数（デフォルト: 90日）}
                            {--dry-run : 実際には削除せず、削除対象を表示するだけ}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '古いファイルを自動削除（領収書、Excel、プロフィール画像など）';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $dryRun = $this->option('dry-run');
        $cutoffDate = Carbon::now()->subDays($days);
        
        $this->info("{$days}日より古いファイルを削除します（基準日: {$cutoffDate->format('Y-m-d')}）");
        
        if ($dryRun) {
            $this->warn('【DRY RUNモード】実際には削除しません。');
        }

        $disk = Storage::disk('public');
        $deletedCount = 0;
        $totalSize = 0;

        // 各ディレクトリをチェック
        $directories = ['receipts', 'expenses', 'travel-requests', 'profiles', 'news', 'documents'];
        
        foreach ($directories as $directory) {
            if (!$disk->exists($directory)) {
                continue;
            }

            $files = $disk->files($directory);
            
            foreach ($files as $file) {
                $lastModified = Carbon::createFromTimestamp($disk->lastModified($file));
                
                if ($lastModified->lt($cutoffDate)) {
                    $size = $disk->size($file);
                    $totalSize += $size;
                    
                    if ($dryRun) {
                        $this->line("削除対象: {$file} (最終更新: {$lastModified->format('Y-m-d H:i')}, サイズ: " . $this->formatBytes($size) . ")");
                    } else {
                        if ($disk->delete($file)) {
                            $deletedCount++;
                            $this->line("削除: {$file}");
                        } else {
                            $this->error("削除失敗: {$file}");
                        }
                    }
                }
            }
        }

        if ($dryRun) {
            $this->info("\n【DRY RUN結果】削除対象ファイルの合計サイズ: " . $this->formatBytes($totalSize));
        } else {
            $this->info("\n削除完了: {$deletedCount}個のファイルを削除しました（合計サイズ: " . $this->formatBytes($totalSize) . "）");
        }

        return Command::SUCCESS;
    }

    /**
     * バイト数を読みやすい形式に変換
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}

