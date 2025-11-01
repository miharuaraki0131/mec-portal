<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('会社の色々') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- 機能カード -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                <!-- 社員情報 -->
                <a href="{{ route('users.index') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center mb-3">
                            <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <h3 class="ml-3 text-lg font-semibold text-gray-900">社員情報</h3>
                        </div>
                        <p class="text-gray-600 text-base">部署・課ごとの社員一覧を確認</p>
                    </div>
                </a>

                <!-- 連絡先 -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center mb-3">
                            <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h3 class="ml-3 text-lg font-semibold text-gray-900">連絡先</h3>
                        </div>
                        <div class="text-gray-600 text-base space-y-3 mt-4">
                            <div>
                                <p class="font-medium text-gray-700 mb-1">本社</p>
                                <p class="text-xs">〒532-0011</p>
                                <p class="text-xs">大阪府大阪市淀川区西中島2丁目12番11号</p>
                                <p class="text-xs">川久センタービル2階</p>
                                <p class="text-xs mt-1">TEL: <a href="tel:06-6305-5301" class="text-indigo-600 hover:underline">06-6305-5301</a></p>
                                <p class="text-xs">FAX: 06-6302-4453</p>
                            </div>
                            <div class="pt-2 border-t">
                                <p class="font-medium text-gray-700 mb-1">東京事業所</p>
                                <p class="text-xs">〒101-0041</p>
                                <p class="text-xs">東京都千代田区神田須田町1丁目16番5号</p>
                                <p class="text-xs">ヒューリック神田ビル6階</p>
                                <p class="text-xs mt-1">TEL: <a href="tel:03-5244-4552" class="text-indigo-600 hover:underline">03-5244-4552</a></p>
                                <p class="text-xs">FAX: 03-5244-4556</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 各種リンク -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center mb-3">
                            <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
                                <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                </svg>
                            </div>
                            <h3 class="ml-3 text-lg font-semibold text-gray-900">各種リンク</h3>
                        </div>
                        <div class="text-gray-600 text-base space-y-2 mt-4">
                            <a href="https://www.n-mec.com/" target="_blank" rel="noopener noreferrer" class="block hover:text-indigo-600 transition-colors">
                                <span class="font-medium">会社ホームページ</span>
                                <svg class="inline-block w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </a>
                            <a href="https://drive.google.com" target="_blank" rel="noopener noreferrer" class="block hover:text-indigo-600 transition-colors">
                                <span class="font-medium">社内ドライブ</span>
                                <svg class="inline-block w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </a>
                            <a href="https://calendar.google.com" target="_blank" rel="noopener noreferrer" class="block hover:text-indigo-600 transition-colors">
                                <span class="font-medium">社内カレンダー</span>
                                <svg class="inline-block w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

