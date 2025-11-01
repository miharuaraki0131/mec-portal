<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('FAQ詳細') }}
            </h2>
            <a href="{{ route('faqs.index') }}" 
               class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                ← 一覧に戻る
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- カテゴリ -->
                    @if($faq->category)
                        <div class="mb-4">
                            <span class="bg-gray-100 text-gray-800 text-xs px-3 py-1 rounded">
                                {{ $faq->category }}
                            </span>
                        </div>
                    @endif

                    <!-- 質問 -->
                    <div class="mb-6">
                        <h1 class="text-2xl font-bold text-gray-900 mb-3">
                            {{ $faq->question }}
                        </h1>
                    </div>

                    <!-- 回答 -->
                    <div class="mb-6 pb-6 border-b">
                        <div class="text-gray-700 whitespace-pre-wrap leading-relaxed">
                            {!! nl2br(e($faq->answer)) !!}
                        </div>
                    </div>

                    <!-- 統計情報 -->
                    <div class="flex items-center gap-6 text-sm text-gray-500 mb-6">
                        <span>閲覧数: {{ $faq->view_count }}</span>
                        <span>役に立った: {{ $faq->helpful_count }}</span>
                    </div>

                    <!-- 「役に立った」ボタン -->
                    <form method="POST" action="{{ route('faqs.helpful', $faq->id) }}" class="mb-6">
                        @csrf
                        <button type="submit" 
                                class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium transition-colors inline-flex items-center gap-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v3m4 6h.01M5 20h5a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2v9a2 2 0 002 2z" />
                            </svg>
                            この回答は役に立ちました
                        </button>
                    </form>

                    <!-- アクションボタン（編集・削除） -->
                    @can('update', $faq)
                        <div class="flex gap-3 pt-4 border-t">
                            <a href="{{ route('faqs.edit', $faq->id) }}" 
                               class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                編集
                            </a>
                            <form method="POST" action="{{ route('faqs.destroy', $faq->id) }}" 
                                  onsubmit="return confirm('このFAQを削除しますか？');">
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

