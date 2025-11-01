<?php

namespace App\Exceptions;

use Exception;

class AuthorizationException extends Exception
{
    /**
     * Render the exception as an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $this->getMessage() ?: 'この操作を実行する権限がありません。',
            ], 403);
        }

        abort(403, $this->getMessage() ?: 'この操作を実行する権限がありません。');
    }
}

