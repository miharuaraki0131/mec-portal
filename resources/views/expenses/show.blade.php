<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('経費申請詳細') }}
            </h2>
            <a href="{{ route('expenses.index') }}" 
               class="text-indigo-600 hover:text-indigo-800 text-base font-medium">
                ← 一覧に戻る
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- ステータス -->
                    <div class="flex items-center gap-3 mb-4">
                        <span class="bg-{{ $expense->status_color }}-100 text-{{ $expense->status_color }}-800 text-xs font-semibold px-3 py-1 rounded">
                            {{ $expense->status_label }}
                        </span>
                    </div>

                    <!-- 申請者情報 -->
                    <div class="mb-6 pb-4 border-b">
                        <h3 class="text-base font-medium text-gray-500 mb-2">申請者情報</h3>
                        <div class="grid grid-cols-2 gap-4 text-base">
                            <div>
                                <span class="text-gray-500">申請者</span>
                                <p class="text-gray-900 font-medium">{{ $expense->user->name }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">社員コード</span>
                                <p class="text-gray-900 font-medium">{{ $expense->user->user_code }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">所属</span>
                                <p class="text-gray-900 font-medium">{{ $expense->user->division ? $expense->user->division->full_name : '未所属' }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">申請日</span>
                                <p class="text-gray-900 font-medium">{{ $expense->created_at->format('Y年m月d日 H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- 経費明細 -->
                    <div class="mb-6 pb-4 border-b">
                        <h3 class="text-base font-medium text-gray-500 mb-4">経費明細</h3>
                        <div class="grid grid-cols-2 gap-4 text-base">
                            <div>
                                <span class="text-gray-500">経費発生日</span>
                                <p class="text-gray-900 font-medium">{{ $expense->date->format('Y年m月d日') }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">費目</span>
                                <p class="text-gray-900 font-medium">{{ $expense->category }}</p>
                            </div>
                            <div class="col-span-2">
                                <span class="text-gray-500">内容</span>
                                <p class="text-gray-900 font-medium">{{ $expense->description }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">金額</span>
                                <p class="text-gray-900 font-medium text-lg">{{ number_format($expense->amount) }}円</p>
                            </div>
                        </div>
                    </div>

                    <!-- 領収書 -->
                    @if($expense->receipt_path)
                        <div class="mb-6 pb-4 border-b">
                            <h3 class="text-base font-medium text-gray-500 mb-2">領収書</h3>
                            <a href="{{ asset('storage/' . $expense->receipt_path) }}" 
                               target="_blank"
                               class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-800">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                領収書を表示
                            </a>
                        </div>
                    @endif

                    <!-- 承認履歴 -->
                    @if($expense->workflowApprovals && $expense->workflowApprovals->count() > 0)
                        <div class="mb-6 pb-4 border-b">
                            <h3 class="text-base font-medium text-gray-500 mb-4">承認履歴</h3>
                            <div class="space-y-3">
                                @foreach($expense->workflowApprovals as $approval)
                                    <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="text-base font-medium text-gray-900">{{ $approval->approver->name ?? '未設定' }}</span>
                                                <span class="bg-{{ $approval->status_color }}-100 text-{{ $approval->status_color }}-800 text-xs font-semibold px-2 py-1 rounded">
                                                    {{ $approval->status_label }}
                                                </span>
                                            </div>
                                            @if($approval->comment)
                                                <p class="text-base text-gray-600">{{ $approval->comment }}</p>
                                            @endif
                                            @if($approval->approved_at)
                                                <p class="text-xs text-gray-500 mt-1">{{ $approval->approved_at->format('Y年m月d日 H:i') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- アクションボタン -->
                    <div class="flex gap-3 pt-4 border-t">
                        @can('view', $expense)
                            <a href="{{ route('expenses.download-excel', $expense->id) }}" 
                               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-base font-medium transition-colors flex items-center gap-2">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Excelをダウンロード
                            </a>
                        @endcan
                        @can('update', $expense)
                            <a href="{{ route('expenses.edit', $expense->id) }}" 
                               class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-base font-medium transition-colors">
                                @if($expense->status === \App\Models\Expense::STATUS_REJECTED)
                                    再申請
                                @else
                                    編集
                                @endif
                            </a>
                            <form method="POST" action="{{ route('expenses.destroy', $expense->id) }}" 
                                  onsubmit="return confirm('この経費申請を削除しますか？');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-base font-medium transition-colors">
                                    削除
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

