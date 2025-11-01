<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('FAQ編集') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('faqs.update', $faq->id) }}">
                        @csrf
                        @method('PUT')

                        <!-- 質問 -->
                        <div class="mb-6">
                            <label for="question" class="block text-base font-medium text-gray-700 mb-2">
                                質問 <span class="text-red-500">*</span>
                            </label>
                            <textarea id="question" 
                                      name="question" 
                                      rows="3"
                                      required
                                      class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">{{ old('question', $faq->question) }}</textarea>
                            @error('question')
                                <p class="mt-1 text-base text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 回答 -->
                        <div class="mb-6">
                            <label for="answer" class="block text-base font-medium text-gray-700 mb-2">
                                回答 <span class="text-red-500">*</span>
                            </label>
                            <textarea id="answer" 
                                      name="answer" 
                                      rows="8"
                                      required
                                      class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">{{ old('answer', $faq->answer) }}</textarea>
                            @error('answer')
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
                                   value="{{ old('category', $faq->category) }}"
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
                            <a href="{{ route('faqs.show', $faq->id) }}" 
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

