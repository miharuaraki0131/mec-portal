<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $news = News::with('postedBy')
            ->published()
            ->priority()
            ->latest('published_at')
            ->paginate(10);

        return view('news.index', compact('news'));
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
    public function store(Request $request)
    {
        $this->authorize('create', News::class);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'category' => ['nullable', 'string', 'max:100'],
            'priority' => ['nullable', 'integer', 'in:0,1'],
            'published_at' => ['nullable', 'date'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:5120'], // 5MB
        ]);

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

        News::create($validated);

        return redirect()->route('news.index')
            ->with('success', 'お知らせを投稿しました。');
    }

    /**
     * Display the specified resource.
     */
    public function show(News $news)
    {
        $news->incrementViewCount();
        
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
    public function update(Request $request, News $news)
    {
        $this->authorize('update', $news);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'category' => ['nullable', 'string', 'max:100'],
            'priority' => ['nullable', 'integer', 'in:0,1'],
            'published_at' => ['nullable', 'date'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:5120'], // 5MB
        ]);

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

        $news->update($validated);

        return redirect()->route('news.index')
            ->with('success', 'お知らせを更新しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(News $news)
    {
        $this->authorize('delete', $news);

        // 画像を削除
        if ($news->image_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($news->image_path);
        }

        $news->delete();

        return redirect()->route('news.index')
            ->with('success', 'お知らせを削除しました。');
    }
}
