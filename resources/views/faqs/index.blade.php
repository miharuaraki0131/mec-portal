<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('FAQ（よくある質問）') }}
            </h2>
            @can('create', App\Models\FAQ::class)
                <a href="{{ route('faqs.create') }}" 
                   class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    新規登録
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- 検索・フィルター -->
            <div class="bg-white p-6 rounded-lg shadow-sm mb-6">
                <form method="GET" action="{{ route('faqs.index') }}" class="space-y-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                            検索
                        </label>
                        <div class="flex gap-2">
                            <input type="text" 
                                   id="search" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="質問や回答を検索..."
                                   class="flex-1 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                            <button type="submit" 
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors">
                                検索
                            </button>
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap gap-4">
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-1">カテゴリ</label>
                            <select name="category" id="category" class="rounded-lg border-gray-300 text-sm">
                                <option value="">すべて</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                                        {{ $cat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @if(request('search') || request('category'))
                            <div class="flex items-end">
                                <a href="{{ route('faqs.index') }}" 
                                   class="text-sm text-gray-600 hover:text-gray-800 underline">
                                    フィルターをクリア
                                </a>
                            </div>
                        @endif
                    </div>
                </form>
            </div>

            @if($faqs->count() > 0)
                <div class="space-y-4">
                    @foreach($faqs as $faq)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                            <a href="{{ route('faqs.show', $faq->id) }}" class="block p-6">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        @if($faq->category)
                                            <span class="inline-block bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded mb-2">
                                                {{ $faq->category }}
                                            </span>
                                        @endif
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                            {{ $faq->question }}
                                        </h3>
                                        <p class="text-gray-600 text-sm line-clamp-2 mb-3">
                                            {{ Str::limit($faq->answer, 150) }}
                                        </p>
                                        <div class="flex items-center gap-4 text-xs text-gray-500">
                                            <span>閲覧数: {{ $faq->view_count }}</span>
                                            <span>役に立った: {{ $faq->helpful_count }}</span>
                                        </div>
                                    </div>
                                    <svg class="h-5 w-5 text-gray-400 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                <!-- ページネーション -->
                <div class="mt-6">
                    {{ $faqs->links() }}
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center text-gray-500">
                        @if(request('search') || request('category'))
                            検索条件に一致するFAQはありません。
                        @else
                            FAQはまだ登録されていません。
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

