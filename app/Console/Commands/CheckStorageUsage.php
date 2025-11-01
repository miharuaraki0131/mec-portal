<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CheckStorageUsage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:check-usage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ストレージ使用量を確認';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $disk = Storage::disk('public');
        $directories = ['receipts', 'expenses', 'travel-requests', 'profiles', 'news', 'documents'];
        
        $this->info('ストレージ使用量の確認');
        $this->info('========================');
        
        $totalSize = 0;
        $totalFiles = 0;
        
        foreach ($directories as $directory) {
            if (!$disk->exists($directory)) {
                continue;
            }
            
            $files = $disk->allFiles($directory);
            $dirSize = 0;
            $fileCount = count($files);
            
            foreach ($files as $file) {
                $dirSize += $disk->size($file);
            }
            
            $totalSize += $dirSize;
            $totalFiles += $fileCount;
            
            $this->line("{$directory}: " . $this->formatBytes($dirSize) . " ({$fileCount}個のファイル)");
        }
        
        $this->info('------------------------');
        $this->info("合計: " . $this->formatBytes($totalSize) . " ({$totalFiles}個のファイル)");
        
        // 警告レベル（1GB以上）
        if ($totalSize > 1073741824) {
            $this->warn('⚠️  ストレージ使用量が1GBを超えています。古いファイルの削除を検討してください。');
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

