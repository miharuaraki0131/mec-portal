<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('社内連絡（お知らせ）') }}
            </h2>
            @can('create', App\Models\News::class)
                <a href="{{ route('news.create') }}" 
                   class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-base font-medium transition-colors">
                    新規投稿
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- 検索・フィルター -->
            <div class="bg-white p-6 rounded-lg shadow-sm mb-6">
                <form method="GET" action="{{ route('news.index') }}" class="space-y-4">
                    <div>
                        <label for="search" class="block text-base font-medium text-gray-700 mb-2">
                            検索
                        </label>
                        <div class="flex gap-2">
                            <input type="text" 
                                   id="search" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="タイトルや内容を検索..."
                                   class="flex-1 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base">
                            <button type="submit" 
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg text-base font-medium transition-colors">
                                検索
                            </button>
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap gap-4">
                        <div>
                            <label for="category" class="block text-base font-medium text-gray-700 mb-1">カテゴリ</label>
                            <select name="category" id="category" class="rounded-lg border-gray-300 text-base">
                                <option value="">すべて</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                                        {{ $cat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="priority" class="block text-base font-medium text-gray-700 mb-1">重要度</label>
                            <select name="priority" id="priority" class="rounded-lg border-gray-300 text-base">
                                <option value="">すべて</option>
                                <option value="1" {{ request('priority') === '1' ? 'selected' : '' }}>重要</option>
                                <option value="0" {{ request('priority') === '0' ? 'selected' : '' }}>通常</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="submit" 
                                    class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-base font-medium transition-colors">
                                フィルター適用
                            </button>
                        </div>
                        @if(request('search') || request('category') || request('priority') !== '')
                            <div class="flex items-end">
                                <a href="{{ route('news.index') }}" 
                                   class="text-gray-600 hover:text-gray-800 text-base underline">
                                    クリア
                                </a>
                            </div>
                        @endif
                    </div>
                </form>
            </div>

            @if($news->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="divide-y divide-gray-200">
                        @foreach($news as $item)
                            <a href="{{ route('news.show', $item->id) }}" 
                               class="block hover:bg-gray-50 transition-colors">
                                <div class="p-6">
                                    <div class="flex items-start gap-4">
                                        @if($item->image_path)
                                            <div class="flex-shrink-0">
                                                <img src="{{ asset('storage/' . $item->image_path) }}" 
                                                     alt="{{ $item->title }}" 
                                                     class="w-24 h-24 object-cover rounded-lg border border-gray-300">
                                            </div>
                                        @endif
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3 mb-2">
                                                @if($item->priority === 1)
                                                    <span class="bg-red-100 text-red-800 text-xs font-semibold px-2 py-1 rounded">
                                                        重要
                                                    </span>
                                                @endif
                                                @if($item->category)
                                                    <span class="bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded">
                                                        {{ $item->category }}
                                                    </span>
                                                @endif
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                                {{ $item->title }}
                                            </h3>
                                            <p class="text-gray-600 text-base line-clamp-2 mb-3">
                                                {{ Str::limit(strip_tags($item->content), 100) }}
                                            </p>
                                            <div class="flex items-center gap-4 text-xs text-gray-500">
                                                <span>投稿者: {{ $item->postedBy->name ?? '不明' }}</span>
                                                <span>公開日: {{ $item->published_at ? $item->published_at->format('Y/m/d') : '未設定' }}</span>
                                                <span>閲覧数: {{ $item->view_count }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- ページネーション -->
                <div class="mt-4">
                    {{ $news->links() }}
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center text-gray-500">
                        お知らせはありません。
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

