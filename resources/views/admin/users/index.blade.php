<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('ユーザー管理') }}
            </h2>
            <div class="flex gap-3">
                <a href="{{ route('admin.masters.index') }}" 
                   class="text-gray-600 hover:text-gray-900 text-base font-medium">
                    ← マスタ管理に戻る
                </a>
                <a href="{{ route('admin.users.create') }}" 
                   class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-base font-medium transition-colors">
                    新規登録
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- フィルター -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4">
                    <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-base font-medium text-gray-700 mb-1">検索</label>
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="名前、コード、メール"
                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-200 text-base">
                        </div>
                        <div>
                            <label class="block text-base font-medium text-gray-700 mb-1">部署</label>
                            <select name="division_id" 
                                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-200 text-base">
                                <option value="">すべて</option>
                                @foreach($divisions as $parent)
                                    @if($parent->children->count() > 0)
                                        <optgroup label="{{ $parent->name }}">
                                            @foreach($parent->children as $child)
                                                <option value="{{ $child->id }}" {{ request('division_id') == $child->id ? 'selected' : '' }}>
                                                    {{ $parent->name }} > {{ $child->name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @else
                                        <option value="{{ $parent->id }}" {{ request('division_id') == $parent->id ? 'selected' : '' }}>
                                            {{ $parent->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-base font-medium text-gray-700 mb-1">ロール</label>
                            <select name="role" 
                                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-200 text-base">
                                <option value="">すべて</option>
                                <option value="0" {{ request('role') === '0' ? 'selected' : '' }}>一般ユーザー</option>
                                <option value="1" {{ request('role') === '1' ? 'selected' : '' }}>管理者</option>
                                <option value="2" {{ request('role') === '2' ? 'selected' : '' }}>部署責任者</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="submit" 
                                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                                絞り込み
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @if($users->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">社員コード</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">氏名</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">メール</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">所属</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ロール</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">操作</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($users as $user)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-base text-gray-900">
                                            {{ $user->user_code }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-base text-gray-900">
                                            {{ $user->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-base text-gray-900">
                                            {{ $user->email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-base text-gray-900">
                                            {{ $user->division ? $user->division->full_name : '未所属' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-base">
                                            @if($user->role === 1)
                                                <span class="bg-red-100 text-red-800 text-xs font-semibold px-2 py-1 rounded">管理者</span>
                                            @elseif($user->role === 2)
                                                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-1 rounded">部署責任者</span>
                                            @else
                                                <span class="bg-gray-100 text-gray-800 text-xs font-semibold px-2 py-1 rounded">一般ユーザー</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-base font-medium">
                                            <div class="flex justify-end gap-2">
                                                <a href="{{ route('admin.users.edit', $user->id) }}" 
                                                   class="text-indigo-600 hover:text-indigo-900">編集</a>
                                                <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" 
                                                      onsubmit="return confirm('このユーザーを削除しますか？');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">削除</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ページネーション -->
                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center text-gray-500">
                        ユーザーが見つかりません。
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

