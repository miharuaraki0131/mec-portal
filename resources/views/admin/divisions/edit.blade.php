<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('部署編集') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.divisions.update', $division->id) }}">
                        @csrf
                        @method('PUT')

                        <!-- 部署名 -->
                        <div class="mb-4">
                            <label for="name" class="block text-base font-medium text-gray-700 mb-1">
                                部署名 <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $division->name) }}"
                                   required
                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-200 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="mt-1 text-base text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 親部署 -->
                        <div class="mb-4">
                            <label for="parent_id" class="block text-base font-medium text-gray-700 mb-1">
                                親部署
                            </label>
                            <select id="parent_id" 
                                    name="parent_id" 
                                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-200 @error('parent_id') border-red-500 @enderror">
                                <option value="">なし（親部署）</option>
                                @foreach($divisions as $parent)
                                    <option value="{{ $parent->id }}" {{ old('parent_id', $division->parent_id) == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">親部署を選択すると、その部署の子部署（課）として登録されます</p>
                            @error('parent_id')
                                <p class="mt-1 text-base text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 責任者 -->
                        <div class="mb-6">
                            <label for="manager_id" class="block text-base font-medium text-gray-700 mb-1">
                                責任者
                            </label>
                            <select id="manager_id" 
                                    name="manager_id" 
                                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-200 @error('manager_id') border-red-500 @enderror">
                                <option value="">未設定</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('manager_id', $division->manager_id) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->user_code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('manager_id')
                                <p class="mt-1 text-base text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- ボタン -->
                        <div class="flex justify-end gap-3">
                            <a href="{{ route('admin.divisions.index') }}" 
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

