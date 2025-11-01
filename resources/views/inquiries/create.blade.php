<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('新規問い合わせ') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('inquiries.store') }}">
                        @csrf

                        <!-- 件名 -->
                        <div class="mb-4">
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">
                                件名 <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="subject" 
                                   name="subject" 
                                   value="{{ old('subject') }}"
                                   required
                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-200 @error('subject') border-red-500 @enderror">
                            @error('subject')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 送信先部署 -->
                        <div class="mb-4">
                            <label for="department" class="block text-sm font-medium text-gray-700 mb-1">
                                送信先部署 <span class="text-red-500">*</span>
                            </label>
                            <select id="department" 
                                    name="department" 
                                    required
                                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-200 @error('department') border-red-500 @enderror">
                                <option value="">選択してください</option>
                                @foreach($divisions as $parentDivision)
                                    @if($parentDivision->children->count() > 0)
                                        {{-- 親部署とその子部署（課）を表示 --}}
                                        <optgroup label="{{ $parentDivision->name }}">
                                            <option value="{{ $parentDivision->name }}" {{ old('department') === $parentDivision->name ? 'selected' : '' }}>
                                                {{ $parentDivision->name }}
                                            </option>
                                            @foreach($parentDivision->children as $childDivision)
                                                <option value="{{ $childDivision->full_name }}" {{ old('department') === $childDivision->full_name ? 'selected' : '' }}>
                                                    {{ $childDivision->full_name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @else
                                        {{-- 子部署がない親部署（例：業務改善部） --}}
                                        <option value="{{ $parentDivision->name }}" {{ old('department') === $parentDivision->name ? 'selected' : '' }}>
                                            {{ $parentDivision->name }}
                                        </option>
                                    @endif
                                @endforeach
                                <option value="その他" {{ old('department') === 'その他' ? 'selected' : '' }}>その他</option>
                            </select>
                            @error('department')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- メッセージ -->
                        <div class="mb-6">
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-1">
                                メッセージ <span class="text-red-500">*</span>
                            </label>
                            <textarea id="message" 
                                      name="message" 
                                      rows="8"
                                      required
                                      class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-200 @error('message') border-red-500 @enderror">{{ old('message') }}</textarea>
                            @error('message')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- ボタン -->
                        <div class="flex justify-end gap-3">
                            <a href="{{ route('inquiries.index') }}" 
                               class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium transition-colors">
                                キャンセル
                            </a>
                            <button type="submit" 
                                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors">
                                送信する
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

