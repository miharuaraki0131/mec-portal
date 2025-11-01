<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('経費精算') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- 申請カード -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- 経費 -->
                <a href="{{ route('expenses.create', ['type' => 'expense']) }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center mb-3">
                            <div class="flex-shrink-0 bg-emerald-100 rounded-lg p-3">
                                <svg class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h3 class="ml-3 text-lg font-semibold text-gray-900">経費</h3>
                        </div>
                        <p class="text-gray-600 text-base">一般経費の申請</p>
                    </div>
                </a>

                <!-- 交通費 -->
                <a href="{{ route('expenses.create', ['type' => 'transportation']) }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center mb-3">
                            <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                                </svg>
                            </div>
                            <h3 class="ml-3 text-lg font-semibold text-gray-900">交通費</h3>
                        </div>
                        <p class="text-gray-600 text-base">交通費の申請</p>
                    </div>
                </a>

                <!-- 出張 -->
                <a href="{{ route('travel-requests.create') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center mb-3">
                            <div class="flex-shrink-0 bg-orange-100 rounded-lg p-3">
                                <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                            </div>
                            <h3 class="ml-3 text-lg font-semibold text-gray-900">出張</h3>
                        </div>
                        <p class="text-gray-600 text-base">出張申請</p>
                    </div>
                </a>
            </div>

            <!-- 申請一覧へのリンク -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">申請一覧</h3>
                            <p class="text-base text-gray-600">過去の申請履歴を確認できます</p>
                        </div>
                        <a href="{{ route('expenses.index') }}" 
                           class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                            一覧を見る
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

