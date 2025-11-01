<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\Division;
use App\Mail\InquiryNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class InquiryController extends Controller
{
    /**
     * Display a listing of the resource.
     * ご意見箱は新規問い合わせフォームのみなので、createにリダイレクト
     */
    public function index()
    {
        return redirect()->route('inquiries.create');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $divisions = Division::getHierarchical();
        return view('inquiries.create', compact('divisions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'department' => 'required|string|max:255',
        ]);

        $inquiry = Inquiry::create([
            'user_id' => Auth::id(),
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'department' => $validated['department'],
            'thread_id' => Inquiry::generateThreadId(),
            'status' => Inquiry::STATUS_PENDING,
        ]);

        // 部署の責任者にメール送信
        $this->sendNotificationToDivisionManager($inquiry);

        return redirect()->route('inquiries.create')
            ->with('success', '問い合わせを送信しました。担当部署の責任者に通知を送信しました。');
    }

    /**
     * Display the specified resource.
     */
    public function show(Inquiry $inquiry)
    {
        $inquiry->load('user', 'repliedBy', 'replies.user', 'replies.repliedBy');
        
        // スレッド内のすべてのメッセージを取得
        $threadMessages = Inquiry::where('thread_id', $inquiry->thread_id)
            ->with('user', 'repliedBy')
            ->orderBy('created_at')
            ->get();

        return view('inquiries.show', compact('inquiry', 'threadMessages'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inquiry $inquiry)
    {
        $this->authorize('update', $inquiry);
        
        $divisions = Division::getHierarchical();
        return view('inquiries.edit', compact('inquiry', 'divisions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inquiry $inquiry)
    {
        $this->authorize('update', $inquiry);

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'department' => 'required|string|max:255',
            'status' => 'nullable|integer|in:0,1,2',
        ]);

        $inquiry->update($validated);

        return redirect()->route('inquiries.show', $inquiry)
            ->with('success', '問い合わせを更新しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inquiry $inquiry)
    {
        $this->authorize('delete', $inquiry);

        $inquiry->delete();

        return redirect()->route('inquiries.index')
            ->with('success', '問い合わせを削除しました。');
    }

    /**
     * 返信を送信
     */
    public function reply(Request $request, Inquiry $inquiry)
    {
        $this->authorize('reply', $inquiry);

        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        $reply = Inquiry::create([
            'user_id' => Auth::id(),
            'subject' => 'Re: ' . $inquiry->subject,
            'message' => $validated['message'],
            'department' => $inquiry->department,
            'parent_id' => $inquiry->id,
            'thread_id' => $inquiry->thread_id,
            'replied_by' => Auth::id(),
            'replied_at' => now(),
            'status' => Inquiry::STATUS_IN_PROGRESS,
        ]);

        // 親問い合わせのステータスを「対応中」に更新
        if ($inquiry->status === Inquiry::STATUS_PENDING) {
            $inquiry->update(['status' => Inquiry::STATUS_IN_PROGRESS]);
        }

        return redirect()->route('inquiries.show', $inquiry)
            ->with('success', '返信を送信しました。');
    }

    /**
     * ステータスを更新
     */
    public function updateStatus(Request $request, Inquiry $inquiry)
    {
        $this->authorize('updateStatus', $inquiry);

        $validated = $request->validate([
            'status' => 'required|integer|in:0,1,2',
        ]);

        $inquiry->update([
            'status' => $validated['status'],
        ]);

        return redirect()->back()
            ->with('success', 'ステータスを更新しました。');
    }

    /**
     * 部署の責任者にメール通知を送信
     */
    private function sendNotificationToDivisionManager(Inquiry $inquiry): void
    {
        // 部署名からDivisionを特定
        // フルパス形式（例: "第1事業部 > 製品開発課"）または単純な部署名をチェック
        $division = null;
        
        // まず、フルパス形式をチェック（" > " を含む場合）
        if (strpos($inquiry->department, ' > ') !== false) {
            [$parentName, $childName] = explode(' > ', $inquiry->department, 2);
            $parent = Division::where('name', $parentName)->whereNull('parent_id')->first();
            if ($parent) {
                $division = Division::where('name', $childName)->where('parent_id', $parent->id)->first();
            }
        } else {
            // 単純な部署名の場合（親部署または子部署）
            $division = Division::where('name', $inquiry->department)->first();
        }

        // 部署が見つかり、責任者が設定されている場合のみ送信
        if ($division && $division->manager_id && $division->manager) {
            Mail::to($division->manager->email)->send(new InquiryNotification($inquiry));
        }
    }
}

