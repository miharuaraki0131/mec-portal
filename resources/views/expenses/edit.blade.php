<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('経費申請編集') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('expenses.update', $expense->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- 日付 -->
                        <div class="mb-4">
                            <label for="date" class="block text-sm font-medium text-gray-700 mb-1">
                                経費発生日 <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   id="date" 
                                   name="date" 
                                   value="{{ old('date', $expense->date->format('Y-m-d')) }}"
                                   required
                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-200 @error('date') border-red-500 @enderror">
                            @error('date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 費目 -->
                        <div class="mb-4">
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-1">
                                費目 <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="category" 
                                   name="category" 
                                   value="{{ old('category', $expense->category) }}"
                                   required
                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-200 @error('category') border-red-500 @enderror">
                            @error('category')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 金額 -->
                        <div class="mb-4">
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">
                                金額 <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   id="amount" 
                                   name="amount" 
                                   value="{{ old('amount', $expense->amount) }}"
                                   min="0"
                                   step="1"
                                   required
                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-200 @error('amount') border-red-500 @enderror">
                            @error('amount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 内容 -->
                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                内容 <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="description" 
                                   name="description" 
                                   value="{{ old('description', $expense->description) }}"
                                   required
                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-200 @error('description') border-red-500 @enderror">
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 領収書 -->
                        <div class="mb-6">
                            <label for="receipt" class="block text-sm font-medium text-gray-700 mb-1">
                                領収書（任意）
                            </label>
                            @if($expense->receipt_path)
                                <div class="mb-2">
                                    <a href="{{ asset('storage/' . $expense->receipt_path) }}" 
                                       target="_blank"
                                       class="text-indigo-600 hover:text-indigo-800 text-sm">
                                        現在の領収書を表示
                                    </a>
                                </div>
                            @endif
                            <input type="file" 
                                   id="receipt" 
                                   name="receipt" 
                                   accept=".jpg,.jpeg,.png,.pdf"
                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-200 @error('receipt') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500">対応形式: JPG, PNG, PDF（最大5MB）</p>
                            @error('receipt')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- ボタン -->
                        <div class="flex justify-end gap-3">
                            <a href="{{ route('expenses.show', $expense->id) }}" 
                               class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium transition-colors">
                                キャンセル
                            </a>
                            <button type="submit" 
                                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors">
                                更新する
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

