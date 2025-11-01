<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('ユーザー編集') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                        @csrf
                        @method('PUT')

                        <!-- 社員コード -->
                        <div class="mb-4">
                            <label for="user_code" class="block text-base font-medium text-gray-700 mb-1">
                                社員コード <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="user_code" 
                                   name="user_code" 
                                   value="{{ old('user_code', $user->user_code) }}"
                                   required
                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-200 @error('user_code') border-red-500 @enderror">
                            @error('user_code')
                                <p class="mt-1 text-base text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 氏名 -->
                        <div class="mb-4">
                            <label for="name" class="block text-base font-medium text-gray-700 mb-1">
                                氏名 <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $user->name) }}"
                                   required
                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-200 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="mt-1 text-base text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- メールアドレス -->
                        <div class="mb-4">
                            <label for="email" class="block text-base font-medium text-gray-700 mb-1">
                                メールアドレス <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $user->email) }}"
                                   required
                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-200 @error('email') border-red-500 @enderror">
                            @error('email')
                                <p class="mt-1 text-base text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- パスワード -->
                        <div class="mb-4">
                            <label for="password" class="block text-base font-medium text-gray-700 mb-1">
                                パスワード（変更する場合のみ入力）
                            </label>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   minlength="8"
                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-200 @error('password') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500">8文字以上</p>
                            @error('password')
                                <p class="mt-1 text-base text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- パスワード確認 -->
                        <div class="mb-4">
                            <label for="password_confirmation" class="block text-base font-medium text-gray-700 mb-1">
                                パスワード（確認）
                            </label>
                            <input type="password" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   minlength="8"
                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-200">
                        </div>

                        <!-- ロール -->
                        <div class="mb-4">
                            <label for="role" class="block text-base font-medium text-gray-700 mb-1">
                                ロール <span class="text-red-500">*</span>
                            </label>
                            <select id="role" 
                                    name="role" 
                                    required
                                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-200 @error('role') border-red-500 @enderror">
                                <option value="0" {{ old('role', $user->role) == 0 ? 'selected' : '' }}>一般ユーザー</option>
                                <option value="1" {{ old('role', $user->role) == 1 ? 'selected' : '' }}>管理者</option>
                                <option value="2" {{ old('role', $user->role) == 2 ? 'selected' : '' }}>部署責任者</option>
                            </select>
                            @error('role')
                                <p class="mt-1 text-base text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 所属部署 -->
                        <div class="mb-6">
                            <label for="division_id" class="block text-base font-medium text-gray-700 mb-1">
                                所属部署
                            </label>
                            <select id="division_id" 
                                    name="division_id" 
                                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-200 @error('division_id') border-red-500 @enderror">
                                <option value="">未所属</option>
                                @foreach($divisions as $parent)
                                    @if($parent->children->count() > 0)
                                        <optgroup label="{{ $parent->name }}">
                                            @foreach($parent->children as $child)
                                                <option value="{{ $child->id }}" {{ old('division_id', $user->division_id) == $child->id ? 'selected' : '' }}>
                                                    {{ $parent->name }} > {{ $child->name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @else
                                        <option value="{{ $parent->id }}" {{ old('division_id', $user->division_id) == $parent->id ? 'selected' : '' }}>
                                            {{ $parent->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('division_id')
                                <p class="mt-1 text-base text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- ボタン -->
                        <div class="flex justify-end gap-3">
                            <a href="{{ route('admin.users.index') }}" 
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

