<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Division;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    use LogsActivity;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Document::with('uploadedBy', 'division')
            ->latest();

        // 全文検索
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        // 部署でフィルタリング
        if ($request->has('division_id')) {
            if ($request->division_id === 'all') {
                // 全般（division_idがnull）を表示
                $query->whereNull('division_id');
            } elseif ($request->division_id) {
                $query->where('division_id', $request->division_id);
            }
        }

        // カテゴリでフィルタリング
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        // ファイルタイプでフィルタリング
        if ($request->has('file_type') && $request->file_type) {
            $query->where('file_type', $request->file_type);
        }

        $documents = $query->paginate(12)->withQueryString();

        // 部署一覧を取得（フィルタ用）
        $divisions = Division::getHierarchical();

        // カテゴリ一覧を取得（フィルタ用）
        $categories = Document::distinct()->pluck('category')->filter()->sort();

        return view('documents.index', compact('documents', 'divisions', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Document::class);
        
        $divisions = Division::getHierarchical();
        
        return view('documents.create', compact('divisions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Document::class);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'max:10240'], // 最大10MB
            'category' => ['nullable', 'string', 'max:100'],
            'division_id' => ['nullable', 'exists:divisions,id'],
        ]);

        // ファイルをアップロード
        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('documents', $fileName, 'public');

        Document::create([
            'title' => $validated['title'],
            'file_path' => $filePath,
            'file_type' => $file->getClientOriginalExtension(),
            'category' => $validated['category'] ?? null,
            'division_id' => $validated['division_id'] ?? null,
            'uploaded_by' => Auth::id(),
        ]);

        return redirect()->route('documents.index')
            ->with('success', '資料をアップロードしました。');
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document)
    {
        $document->incrementViewCount();
        
        return view('documents.show', compact('document'));
    }

    /**
     * Download the file.
     */
    public function download(Document $document)
    {
        $document->incrementViewCount();
        
        $filePath = storage_path('app/public/' . $document->file_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'ファイルが見つかりません。');
        }

        return response()->download($filePath, $document->title . '.' . $document->file_type);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Document $document)
    {
        $this->authorize('update', $document);
        
        $divisions = Division::getHierarchical();
        
        return view('documents.edit', compact('document', 'divisions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Document $document)
    {
        $this->authorize('update', $document);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'division_id' => ['nullable', 'exists:divisions,id'],
            'file' => ['nullable', 'file', 'max:10240'], // オプション
        ]);

        // 新しいファイルがアップロードされた場合
        if ($request->hasFile('file')) {
            // 古いファイルを削除
            Storage::disk('public')->delete($document->file_path);
            
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documents', $fileName, 'public');
            
            $validated['file_path'] = $filePath;
            $validated['file_type'] = $file->getClientOriginalExtension();
        }

        $document->update($validated);

        return redirect()->route('documents.index')
            ->with('success', '資料を更新しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document)
    {
        $this->authorize('delete', $document);

        // ファイルを削除
        // 削除前のデータを保存
        $deletedData = $document->toArray();

        Storage::disk('public')->delete($document->file_path);

        $document->delete();

        // ログ記録
        $this->logDeletion('document', $document->id, $deletedData);

        return redirect()->route('documents.index')
            ->with('success', '資料を削除しました。');
    }
}
