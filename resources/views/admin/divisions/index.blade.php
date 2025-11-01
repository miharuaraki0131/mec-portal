<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('部署管理') }}
            </h2>
            <div class="flex gap-3">
                <a href="{{ route('admin.masters.index') }}" 
                   class="text-gray-600 hover:text-gray-900 text-base font-medium">
                    ← マスタ管理に戻る
                </a>
                <a href="{{ route('admin.divisions.create') }}" 
                   class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-base font-medium transition-colors">
                    新規登録
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- 階層構造表示 -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">部署階層構造</h3>
                    <div class="space-y-2">
                        @foreach($hierarchicalDivisions as $parent)
                            <div class="flex items-center gap-2">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <span class="font-medium text-gray-900">{{ $parent->name }}</span>
                                @if($parent->manager)
                                    <span class="text-xs text-gray-500">(責任者: {{ $parent->manager->name }})</span>
                                @endif
                            </div>
                            @foreach($parent->children as $child)
                                <div class="flex items-center gap-2 ml-8">
                                    <span class="text-gray-400">└</span>
                                    <span class="text-gray-700">{{ $child->name }}</span>
                                    @if($child->manager)
                                        <span class="text-xs text-gray-500">(責任者: {{ $child->manager->name }})</span>
                                    @endif
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- 部署一覧 -->
            @if($divisions->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">部署名</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">親部署</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">責任者</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">所属ユーザー数</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">操作</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($divisions as $division)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-base text-gray-900">
                                            {{ $division->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-base text-gray-900">
                                            {{ $division->parent ? $division->parent->name : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-base text-gray-900">
                                            {{ $division->manager ? $division->manager->name : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-base text-gray-900">
                                            {{ $division->users->count() }}名
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-base font-medium">
                                            <div class="flex justify-end gap-2">
                                                <a href="{{ route('admin.divisions.edit', $division->id) }}" 
                                                   class="text-indigo-600 hover:text-indigo-900">編集</a>
                                                <form method="POST" action="{{ route('admin.divisions.destroy', $division->id) }}" 
                                                      onsubmit="return confirm('この部署を削除しますか？');" class="inline">
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
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center text-gray-500">
                        部署が登録されていません。
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

