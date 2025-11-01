<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('社内連絡（お知らせ）') }}
            </h2>
            @can('create', App\Models\News::class)
                <a href="{{ route('news.create') }}" 
                   class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    新規投稿
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
                                            <p class="text-gray-600 text-sm line-clamp-2 mb-3">
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

