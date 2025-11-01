<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('資料編集') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('documents.update', $document->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- タイトル -->
                        <div class="mb-6">
                            <label for="title" class="block text-base font-medium text-gray-700 mb-2">
                                資料タイトル <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title', $document->title) }}"
                                   required
                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                            @error('title')
                                <p class="mt-1 text-base text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 現在のファイル -->
                        <div class="mb-6">
                            <label class="block text-base font-medium text-gray-700 mb-2">
                                現在のファイル
                            </label>
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <p class="text-base text-gray-700">
                                    {{ $document->title }}.{{ $document->file_type }}
                                </p>
                            </div>
                        </div>

                        <!-- ファイル（変更時のみ） -->
                        <div class="mb-6">
                            <label for="file" class="block text-base font-medium text-gray-700 mb-2">
                                ファイル（変更する場合のみ選択）
                            </label>
                            <input type="file" 
                                   id="file" 
                                   name="file"
                                   accept=".pdf,.xlsx,.xls,.docx,.doc,.pptx,.ppt"
                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                            <p class="mt-1 text-xs text-gray-500">対応形式: PDF, Excel, Word, PowerPoint（最大10MB）</p>
                            @error('file')
                                <p class="mt-1 text-base text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 部署 -->
                        <div class="mb-6">
                            <label for="division_id" class="block text-base font-medium text-gray-700 mb-2">
                                部署
                            </label>
                            <select id="division_id" 
                                    name="division_id" 
                                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                                <option value="">全般</option>
                                @foreach($divisions as $parentDivision)
                                    @if($parentDivision->children->count() > 0)
                                        <optgroup label="{{ $parentDivision->name }}">
                                            <option value="{{ $parentDivision->id }}" {{ old('division_id', $document->division_id) == $parentDivision->id ? 'selected' : '' }}>
                                                {{ $parentDivision->name }}
                                            </option>
                                            @foreach($parentDivision->children as $childDivision)
                                                <option value="{{ $childDivision->id }}" {{ old('division_id', $document->division_id) == $childDivision->id ? 'selected' : '' }}>
                                                    {{ $childDivision->full_name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @else
                                        <option value="{{ $parentDivision->id }}" {{ old('division_id', $document->division_id) == $parentDivision->id ? 'selected' : '' }}>
                                            {{ $parentDivision->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('division_id')
                                <p class="mt-1 text-base text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- カテゴリ -->
                        <div class="mb-6">
                            <label for="category" class="block text-base font-medium text-gray-700 mb-2">
                                カテゴリ
                            </label>
                            <input type="text" 
                                   id="category" 
                                   name="category" 
                                   value="{{ old('category', $document->category) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                            @error('category')
                                <p class="mt-1 text-base text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- ボタン -->
                        <div class="flex gap-3">
                            <button type="submit" 
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                                更新する
                            </button>
                            <a href="{{ route('documents.show', $document->id) }}" 
                               class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded-lg font-medium transition-colors">
                                キャンセル
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

