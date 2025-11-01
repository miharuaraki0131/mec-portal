<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('各種資料') }}
            </h2>
            @can('create', App\Models\Document::class)
                <a href="{{ route('documents.create') }}" 
                   class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    資料をアップロード
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

            <!-- フィルター -->
            <div class="bg-white p-4 rounded-lg shadow-sm mb-6">
                <form method="GET" action="{{ route('documents.index') }}" class="flex flex-wrap gap-4">
                    <div>
                        <label for="division_id" class="block text-sm font-medium text-gray-700 mb-1">部署</label>
                        <select name="division_id" id="division_id" class="rounded-lg border-gray-300 text-sm">
                            <option value="">すべて</option>
                            <option value="all" {{ request('division_id') == 'all' ? 'selected' : '' }}>全般</option>
                            @foreach($divisions as $parentDivision)
                                @if($parentDivision->children->count() > 0)
                                    <optgroup label="{{ $parentDivision->name }}">
                                        <option value="{{ $parentDivision->id }}" {{ request('division_id') == $parentDivision->id ? 'selected' : '' }}>
                                            {{ $parentDivision->name }}
                                        </option>
                                        @foreach($parentDivision->children as $childDivision)
                                            <option value="{{ $childDivision->id }}" {{ request('division_id') == $childDivision->id ? 'selected' : '' }}>
                                                {{ $childDivision->full_name }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @else
                                    <option value="{{ $parentDivision->id }}" {{ request('division_id') == $parentDivision->id ? 'selected' : '' }}>
                                        {{ $parentDivision->name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
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
                    <div>
                        <label for="file_type" class="block text-sm font-medium text-gray-700 mb-1">ファイル形式</label>
                        <select name="file_type" id="file_type" class="rounded-lg border-gray-300 text-sm">
                            <option value="">すべて</option>
                            <option value="pdf" {{ request('file_type') == 'pdf' ? 'selected' : '' }}>PDF</option>
                            <option value="xlsx" {{ request('file_type') == 'xlsx' ? 'selected' : '' }}>Excel</option>
                            <option value="docx" {{ request('file_type') == 'docx' ? 'selected' : '' }}>Word</option>
                            <option value="pptx" {{ request('file_type') == 'pptx' ? 'selected' : '' }}>PowerPoint</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            検索
                        </button>
                    </div>
                </form>
            </div>

            @if($documents->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($documents as $document)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                            <div class="p-6">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                                            {{ $document->title }}
                                        </h3>
                                        <div class="flex flex-wrap gap-2 mb-2">
                                            <span class="inline-block bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded">
                                                {{ $document->division ? $document->division->name : '全般' }}
                                            </span>
                                            @if($document->category)
                                                <span class="inline-block bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded">
                                                    {{ $document->category }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-4 text-xs text-gray-500 mb-4">
                                    <span class="flex items-center gap-1">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        {{ strtoupper($document->file_type) }}
                                    </span>
                                    <span>閲覧数: {{ $document->view_count }}</span>
                                </div>

                                <div class="flex gap-2">
                                    <a href="{{ route('documents.show', $document->id) }}" 
                                       class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium text-center transition-colors">
                                        詳細
                                    </a>
                                    <a href="{{ route('documents.download', $document->id) }}" 
                                       class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                        ダウンロード
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- ページネーション -->
                <div class="mt-6">
                    {{ $documents->links() }}
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center text-gray-500">
                        資料はありません。
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

