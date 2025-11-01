<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('資料詳細') }}
            </h2>
            <a href="{{ route('documents.index') }}" 
               class="text-indigo-600 hover:text-indigo-800 text-base font-medium">
                ← 一覧に戻る
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- タイトル -->
                    <h1 class="text-2xl font-bold text-gray-900 mb-4">
                        {{ $document->title }}
                    </h1>

                    <!-- メタ情報 -->
                    <div class="flex flex-wrap items-center gap-4 text-base text-gray-500 mb-6 pb-4 border-b">
                        <span class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded font-medium">
                            {{ $document->division ? $document->division->name : '全般' }}
                        </span>
                        @if($document->category)
                            <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded">
                                {{ $document->category }}
                            </span>
                        @endif
                        <span class="flex items-center gap-1">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            ファイル形式: {{ strtoupper($document->file_type) }}
                        </span>
                        @if($document->file_size)
                            <span>サイズ: {{ $document->file_size }}</span>
                        @endif
                        <span>アップロード者: {{ $document->uploadedBy->name ?? '不明' }}</span>
                        <span>アップロード日: {{ $document->created_at->format('Y年m月d日') }}</span>
                        <span>閲覧数: {{ $document->view_count }}</span>
                    </div>

                    <!-- アクションボタン -->
                    <div class="flex gap-3 mb-6">
                        <a href="{{ route('documents.download', $document->id) }}" 
                           class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-medium transition-colors inline-flex items-center gap-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            ダウンロード
                        </a>

                        @if(in_array(strtolower($document->file_type), ['pdf']))
                            <a href="{{ $document->file_url }}" target="_blank" 
                               class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-lg font-medium transition-colors inline-flex items-center gap-2">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                プレビュー
                            </a>
                        @endif
                    </div>

                    <!-- アクションボタン（編集・削除） -->
                    @can('update', $document)
                        <div class="flex gap-3 pt-4 border-t">
                            <a href="{{ route('documents.edit', $document->id) }}" 
                               class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-base font-medium transition-colors">
                                編集
                            </a>
                            <form method="POST" action="{{ route('documents.destroy', $document->id) }}" 
                                  onsubmit="return confirm('この資料を削除しますか？');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-base font-medium transition-colors">
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

