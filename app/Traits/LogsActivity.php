<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait LogsActivity
{
    /**
     * 重要な操作をログに記録
     */
    protected function logActivity(string $action, string $model, $modelId, array $details = [])
    {
        $user = auth()->user();
        
        $logData = [
            'action' => $action,
            'model' => $model,
            'model_id' => $modelId,
            'user_id' => $user?->id,
            'user_name' => $user?->name,
            'user_email' => $user?->email,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toDateTimeString(),
            'details' => $details,
        ];

        Log::channel('activity')->info('Activity Log', $logData);
    }

    /**
     * 承認操作をログに記録
     */
    protected function logApproval(string $requestType, $requestId, string $status, $comment = null)
    {
        $this->logActivity('approval', $requestType, $requestId, [
            'status' => $status,
            'comment' => $comment,
        ]);
    }

    /**
     * 削除操作をログに記録
     */
    protected function logDeletion(string $model, $modelId, array $deletedData = [])
    {
        $this->logActivity('delete', $model, $modelId, [
            'deleted_data' => $deletedData,
        ]);
    }

    /**
     * 作成操作をログに記録
     */
    protected function logCreation(string $model, $modelId, array $createdData = [])
    {
        $this->logActivity('create', $model, $modelId, [
            'created_data' => $createdData,
        ]);
    }

    /**
     * 更新操作をログに記録
     */
    protected function logUpdate(string $model, $modelId, array $oldData = [], array $newData = [])
    {
        $this->logActivity('update', $model, $modelId, [
            'old_data' => $oldData,
            'new_data' => $newData,
        ]);
    }
}

