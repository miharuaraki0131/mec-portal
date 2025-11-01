<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        ]);

        $validated['posted_by'] = Auth::id();
        $validated['published_at'] = $validated['published_at'] ?? now();

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
        ]);

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

        $news->delete();

        return redirect()->route('news.index')
            ->with('success', 'お知らせを削除しました。');
    }
}
