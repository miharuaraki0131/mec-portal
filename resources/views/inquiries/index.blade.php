<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('ご意見箱') }}
            </h2>
            <a href="{{ route('inquiries.create') }}" 
               class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                新規問い合わせ
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- フィルター -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4">
                    <form method="GET" action="{{ route('inquiries.index') }}" class="flex flex-wrap gap-4">
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-sm font-medium text-gray-700 mb-1">部署</label>
                            <select name="department" 
                                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-200">
                                <option value="">すべて</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept }}" {{ request('department') === $dept ? 'selected' : '' }}>
                                        {{ $dept }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-sm font-medium text-gray-700 mb-1">ステータス</label>
                            <select name="status" 
                                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-200">
                                <option value="">すべて</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>未対応</option>
                                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>対応中</option>
                                <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>対応済</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="submit" 
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                                絞り込み
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @if($inquiries->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="divide-y divide-gray-200">
                        @foreach($inquiries as $inquiry)
                            <a href="{{ route('inquiries.show', $inquiry->id) }}" 
                               class="block hover:bg-gray-50 transition-colors">
                                <div class="p-6">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3 mb-2">
                                                <span class="bg-{{ $inquiry->status_color }}-100 text-{{ $inquiry->status_color }}-800 text-xs font-semibold px-2 py-1 rounded">
                                                    {{ $inquiry->status_label }}
                                                </span>
                                                <span class="bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded">
                                                    {{ $inquiry->department }}
                                                </span>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                                {{ $inquiry->subject }}
                                            </h3>
                                            <p class="text-gray-600 text-sm line-clamp-2 mb-3">
                                                {{ Str::limit($inquiry->message, 150) }}
                                            </p>
                                            <div class="flex items-center gap-4 text-xs text-gray-500">
                                                <span>送信者: {{ $inquiry->user->name ?? '不明' }}</span>
                                                <span>送信日: {{ $inquiry->created_at->format('Y/m/d H:i') }}</span>
                                                @if(($inquiry->replies_count ?? 0) > 0)
                                                    <span>返信数: {{ $inquiry->replies_count }}</span>
                                                @endif
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
                    {{ $inquiries->links() }}
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center text-gray-500">
                        問い合わせはありません。
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

