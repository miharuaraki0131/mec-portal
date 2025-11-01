<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('お知らせ詳細') }}
            </h2>
            <a href="{{ route('news.index') }}" 
               class="text-indigo-600 hover:text-indigo-800 text-base font-medium">
                ← 一覧に戻る
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- ヘッダー情報 -->
                    <div class="flex items-center gap-3 mb-4">
                        @if($news->priority === 1)
                            <span class="bg-red-100 text-red-800 text-xs font-semibold px-3 py-1 rounded">
                                重要
                            </span>
                        @endif
                        @if($news->category)
                            <span class="bg-gray-100 text-gray-800 text-xs px-3 py-1 rounded">
                                {{ $news->category }}
                            </span>
                        @endif
                    </div>

                    <!-- タイトル -->
                    <h1 class="text-2xl font-bold text-gray-900 mb-4">
                        {{ $news->title }}
                    </h1>

                    <!-- メタ情報 -->
                    <div class="flex items-center gap-4 text-base text-gray-500 mb-6 pb-4 border-b">
                        <span>投稿者: {{ $news->postedBy->name ?? '不明' }}</span>
                        <span>公開日: {{ $news->published_at ? $news->published_at->format('Y年m月d日 H:i') : '未設定' }}</span>
                        <span>閲覧数: {{ $news->view_count }}</span>
                    </div>

                    <!-- 画像 -->
                    @if($news->image_path)
                        <div class="mb-6">
                            <img src="{{ asset('storage/' . $news->image_path) }}" 
                                 alt="{{ $news->title }}" 
                                 class="w-full max-w-3xl mx-auto rounded-lg shadow-md">
                        </div>
                    @endif

                    <!-- 本文 -->
                    <div class="prose max-w-none mb-6">
                        <div class="text-gray-700 whitespace-pre-wrap">
                            {!! nl2br(e($news->content)) !!}
                        </div>
                    </div>

                    <!-- アクションボタン -->
                    @can('update', $news)
                        <div class="flex gap-3 pt-4 border-t">
                            <a href="{{ route('news.edit', $news->id) }}" 
                               class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-base font-medium transition-colors">
                                編集
                            </a>
                            <form method="POST" 
                                  action="{{ route('news.destroy', $news->id) }}"
                                  data-confirm="このお知らせを削除しますか？"
                                  data-confirm-title="お知らせの削除">
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

