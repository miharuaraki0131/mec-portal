<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('社員紹介') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- フィルター -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4">
                    <form method="GET" action="{{ route('users.index') }}" class="flex flex-wrap gap-4">
                        <div class="flex-1 min-w-[200px]">
                            <label for="division_id" class="block text-sm font-medium text-gray-700 mb-1">
                                部署で絞り込み
                            </label>
                            <select name="division_id" 
                                    id="division_id"
                                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-200">
                                <option value="">すべての部署</option>
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
                        <div class="flex items-end">
                            <button type="submit" 
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                                絞り込み
                            </button>
                            @if(request('division_id'))
                                <a href="{{ route('users.index') }}" 
                                   class="ml-2 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium transition-colors">
                                    クリア
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- 社員一覧（部署ごとにグループ化） -->
            @if($usersByDivision->count() > 0)
                @foreach($usersByDivision as $divisionName => $users)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b-2 border-indigo-500">
                                {{ $divisionName }}
                                <span class="text-sm font-normal text-gray-500 ml-2">
                                    ({{ $users->count() }}名)
                                </span>
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                                @foreach($users as $user)
                                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                        <div class="flex items-start gap-3">
                                            <div class="flex-shrink-0 w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                                                <span class="text-indigo-600 font-semibold text-lg">
                                                    {{ mb_substr($user->name, 0, 1) }}
                                                </span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="text-sm font-semibold text-gray-900 truncate">
                                                    {{ $user->name }}
                                                </h4>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    社員コード: {{ $user->user_code }}
                                                </p>
                                                @if($user->email)
                                                    <p class="text-xs text-gray-500 truncate mt-1">
                                                        {{ $user->email }}
                                                    </p>
                                                @endif
                                                @if($user->role === 1)
                                                    <span class="inline-block mt-1 px-2 py-0.5 text-xs bg-red-100 text-red-800 rounded">
                                                        管理者
                                                    </span>
                                                @elseif($user->role === 2)
                                                    <span class="inline-block mt-1 px-2 py-0.5 text-xs bg-blue-100 text-blue-800 rounded">
                                                        マネージャー
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center text-gray-500">
                        該当する社員が見つかりませんでした。
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

