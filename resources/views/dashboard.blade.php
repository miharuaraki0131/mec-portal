<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('ダッシュボード') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- FAQ検索フォーム -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">FAQ検索</h3>
                    <form method="GET" action="{{ route('faqs.index') }}" class="flex gap-2">
                        <input type="text" 
                               name="search" 
                               placeholder="よくある質問を検索..."
                               class="flex-1 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200"
                               value="{{ request('search') }}">
                        <button type="submit" 
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                            検索
                        </button>
                    </form>
                </div>
            </div>

            <!-- 社内機能カード -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                <!-- 社内連絡（お知らせ） -->
                <a href="{{ route('news.index') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center mb-3">
                            <div class="flex-shrink-0 bg-indigo-100 rounded-lg p-3">
                                <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </div>
                            <h3 class="ml-3 text-lg font-semibold text-gray-900">社内連絡</h3>
                        </div>
                        <p class="text-gray-600 text-sm">お知らせ・連絡事項を確認</p>
                    </div>
                </a>

                <!-- 会社の色々 -->
                <a href="{{ route('company-info.index') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center mb-3">
                            <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <h3 class="ml-3 text-lg font-semibold text-gray-900">会社の色々</h3>
                        </div>
                        <p class="text-gray-600 text-sm">会社情報・各種リンク</p>
                    </div>
                </a>

                <!-- 各種資料（ドキュメント） -->
                <a href="{{ route('documents.index') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center mb-3">
                            <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h3 class="ml-3 text-lg font-semibold text-gray-900">各種資料</h3>
                        </div>
                        <p class="text-gray-600 text-sm">PDF・Excel等の資料を閲覧</p>
                    </div>
                </a>

                <!-- ご意見箱（問い合わせ） -->
                <a href="{{ route('inquiries.create') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center mb-3">
                            <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
                                <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                </svg>
                            </div>
                            <h3 class="ml-3 text-lg font-semibold text-gray-900">ご意見箱</h3>
                        </div>
                        <p class="text-gray-600 text-sm">質問・要望を送信</p>
                    </div>
                </a>

                <!-- 経費精算 -->
                <a href="{{ route('expenses.menu') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center mb-3">
                            <div class="flex-shrink-0 bg-emerald-100 rounded-lg p-3">
                                <svg class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="ml-3 text-lg font-semibold text-gray-900">経費精算</h3>
                        </div>
                        <p class="text-gray-600 text-sm">経費申請・精算</p>
                    </div>
                </a>

                <!-- FAQ -->
                <a href="{{ route('faqs.index') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center mb-3">
                            <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-3">
                                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="ml-3 text-lg font-semibold text-gray-900">FAQ</h3>
                        </div>
                        <p class="text-gray-600 text-sm">よくある質問を検索</p>
                    </div>
                </a>
            </div>

            <!-- お知らせ一覧 -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">最新のお知らせ</h3>
                        <a href="{{ route('news.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                            すべて見る →
                        </a>
                    </div>
                    @if($latestNews->count() > 0)
                        <div class="divide-y divide-gray-200">
                            @foreach($latestNews as $news)
                                <a href="{{ route('news.show', $news->id) }}" class="block py-3 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-start gap-3">
                                        @if($news->priority === 1)
                                            <span class="bg-red-100 text-red-800 text-xs font-semibold px-2 py-1 rounded flex-shrink-0">
                                                重要
                                            </span>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-sm font-medium text-gray-900 truncate">
                                                {{ $news->title }}
                                            </h4>
                                            <div class="flex items-center gap-2 mt-1 text-xs text-gray-500">
                                                <span>{{ $news->published_at ? $news->published_at->format('Y/m/d') : '' }}</span>
                                                @if($news->category)
                                                    <span>•</span>
                                                    <span>{{ $news->category }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">お知らせはありません。</p>
                    @endif
                </div>
            </div>

            <!-- 最近アップロードされた資料 -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">最近アップロードされた資料</h3>
                        <a href="{{ route('documents.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                            すべて見る →
                        </a>
                    </div>
                    @if($recentDocuments->count() > 0)
                        <div class="divide-y divide-gray-200">
                            @foreach($recentDocuments as $document)
                                <a href="{{ route('documents.show', $document->id) }}" class="block py-3 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-sm font-medium text-gray-900 truncate">
                                                {{ $document->title }}
                                            </h4>
                                            <div class="flex items-center gap-2 mt-1 text-xs text-gray-500">
                                                <span class="flex items-center gap-1">
                                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    {{ strtoupper($document->file_type) }}
                                                </span>
                                                @if($document->category)
                                                    <span>•</span>
                                                    <span>{{ $document->category }}</span>
                                                @endif
                                                <span>•</span>
                                                <span>{{ $document->created_at->format('Y/m/d') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">資料はありません。</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
