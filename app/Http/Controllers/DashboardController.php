<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Document;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // 最新のお知らせを5件取得
        $latestNews = News::with('postedBy')
            ->published()
            ->priority()
            ->latest('published_at')
            ->limit(5)
            ->get();

        // 最近アップロードされた資料を5件取得
        $recentDocuments = Document::with('uploadedBy')
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', compact('latestNews', 'recentDocuments'));
    }
}

