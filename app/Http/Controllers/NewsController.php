<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewsRequest;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = News::with('postedBy')
            ->published()
            ->priority();

        // 全文検索
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        // カテゴリでフィルタリング
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        // 重要度でフィルタリング
        if ($request->has('priority') && $request->priority !== '') {
            $query->where('priority', $request->priority);
        }

        $news = $query->latest('published_at')->paginate(10)->withQueryString();

        // カテゴリ一覧を取得（フィルタ用）
        $categories = News::published()
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->sort()
            ->values();

        return view('news.index', compact('news', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', News::class);
        
        return view('news.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NewsRequest $request)
    {
        $this->authorize('create', News::class);

        $validated = $request->validated();

        // 画像のアップロード
        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $imagePath = $file->storeAs('news', $fileName, 'public');
        }

        $validated['posted_by'] = Auth::id();
        $validated['published_at'] = $validated['published_at'] ?? now();
        $validated['image_path'] = $imagePath;

        $news = News::create($validated);

        // ログ記録
        $this->logCreation('news', $news->id, $news->toArray());

        return redirect()->route('news.index')
            ->with('success', 'お知らせを投稿しました。');
    }

    /**
     * Display the specified resource.
     */
    public function show(News $news)
    {
        $news->incrementViewCount();
        
        // リレーションを事前読み込み（N+1問題の回避）
        $news->load('postedBy');
        
        return view('news.show', compact('news'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(News $news)
    {
        $this->authorize('update', $news);
        
        return view('news.edit', compact('news'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(NewsRequest $request, News $news)
    {
        $this->authorize('update', $news);

        $validated = $request->validated();

        // 画像の更新
        if ($request->hasFile('image')) {
            // 古い画像を削除
            if ($news->image_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($news->image_path);
            }
            
            $file = $request->file('image');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $validated['image_path'] = $file->storeAs('news', $fileName, 'public');
        }

        $oldData = $news->toArray();
        $news->update($validated);

        // ログ記録
        $this->logUpdate('news', $news->id, $oldData, $news->fresh()->toArray());

        return redirect()->route('news.index')
            ->with('success', 'お知らせを更新しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(News $news)
    {
        $this->authorize('delete', $news);

        // 削除前のデータを保存
        $deletedData = $news->toArray();

        // 画像を削除
        if ($news->image_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($news->image_path);
        }

        $news->delete();

        // ログ記録
        $this->logDeletion('news', $news->id, $deletedData);

        return redirect()->route('news.index')
            ->with('success', 'お知らせを削除しました。');
    }
}
