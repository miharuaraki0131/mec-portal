<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('経費申請詳細') }}
            </h2>
            <a href="{{ route('expenses.index') }}" 
               class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
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
                        <h3 class="text-sm font-medium text-gray-500 mb-2">申請者情報</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
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
                        <h3 class="text-sm font-medium text-gray-500 mb-4">経費明細</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
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
                            <h3 class="text-sm font-medium text-gray-500 mb-2">領収書</h3>
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

                    <!-- アクションボタン -->
                    @can('update', $expense)
                        <div class="flex gap-3 pt-4 border-t">
                            <a href="{{ route('expenses.edit', $expense->id) }}" 
                               class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                編集
                            </a>
                            <form method="POST" action="{{ route('expenses.destroy', $expense->id) }}" 
                                  onsubmit="return confirm('この経費申請を削除しますか？');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                    削除
                                </button>
                            </form>
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

