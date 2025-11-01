<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'mec-portal') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased bg-gray-100">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <div class="w-full sm:max-w-2xl mt-6 px-6 py-8 bg-white shadow-lg overflow-hidden sm:rounded-lg">
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-gray-800 mb-2">
                    {{ config('app.name', 'mec-portal') }}
                </h1>
                <p class="text-gray-600 text-lg">
                    社内ポータルサイト
                </p>
            </div>

            <div class="mb-8">
                <p class="text-center text-gray-700 mb-6">
                    日本メカトロン社内の情報・申請・資料を一元管理するポータルサイトです。
                </p>
                <p class="text-center text-base text-gray-500 mb-6">
                    「探す・申請する・共有する」をひとつの画面で完結させ、<br>
                    業務効率化と承認フローの透明化を目的とします。
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @auth
                    <a href="{{ route('dashboard') }}" 
                       class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        ダッシュボードへ
                    </a>
                @else
                    <a href="{{ route('login') }}" 
                       class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        ログイン
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" 
                           class="inline-flex items-center px-6 py-3 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            新規登録
                        </a>
                    @endif
                @endauth
            </div>

            <div class="mt-8 pt-6 border-t border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center text-base text-gray-600">
                    <div>
                        <p class="font-semibold text-gray-800 mb-1">📊 ダッシュボード</p>
                        <p>お知らせ・申請状況を一覧表示</p>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 mb-1">💴 経費・出張申請</p>
                        <p>承認フローの管理</p>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 mb-1">📁 情報共有</p>
                        <p>ドキュメント・FAQ検索</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
