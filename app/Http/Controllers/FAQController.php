<?php

namespace App\Http\Controllers;

use App\Models\FAQ;
use Illuminate\Http\Request;

class FAQController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = FAQ::query();

        // 検索キーワード
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        // カテゴリでフィルタリング
        if ($request->has('category') && $request->category) {
            $query->byCategory($request->category);
        }

        $faqs = $query->orderBy('view_count', 'desc')
            ->orderBy('helpful_count', 'desc')
            ->latest()
            ->paginate(15);

        // カテゴリ一覧を取得（フィルタ用）
        $categories = FAQ::distinct()->pluck('category')->filter()->sort();

        return view('faqs.index', compact('faqs', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', FAQ::class);
        
        return view('faqs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', FAQ::class);

        $validated = $request->validate([
            'question' => ['required', 'string'],
            'answer' => ['required', 'string'],
            'category' => ['nullable', 'string', 'max:100'],
        ]);

        FAQ::create($validated);

        return redirect()->route('faqs.index')
            ->with('success', 'FAQを登録しました。');
    }

    /**
     * Display the specified resource.
     */
    public function show(FAQ $faq)
    {
        $faq->incrementViewCount();
        
        return view('faqs.show', compact('faq'));
    }

    /**
     * 「役に立った」を記録
     */
    public function helpful(FAQ $faq)
    {
        $faq->incrementHelpfulCount();
        
        return back()->with('success', 'ありがとうございます！');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FAQ $faq)
    {
        $this->authorize('update', $faq);
        
        return view('faqs.edit', compact('faq'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FAQ $faq)
    {
        $this->authorize('update', $faq);

        $validated = $request->validate([
            'question' => ['required', 'string'],
            'answer' => ['required', 'string'],
            'category' => ['nullable', 'string', 'max:100'],
        ]);

        $faq->update($validated);

        return redirect()->route('faqs.index')
            ->with('success', 'FAQを更新しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FAQ $faq)
    {
        $this->authorize('delete', $faq);

        $faq->delete();

        return redirect()->route('faqs.index')
            ->with('success', 'FAQを削除しました。');
    }
}
