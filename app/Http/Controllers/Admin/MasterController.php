<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MasterController extends Controller
{
    /**
     * マスタ管理のトップページ
     */
    public function index()
    {
        // 管理者のみアクセス可能
        if (auth()->user()->role !== 1) {
            abort(403);
        }

        return view('admin.masters.index');
    }
}

