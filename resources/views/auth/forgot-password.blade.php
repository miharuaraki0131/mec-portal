<!DOCTYPE html>
<html>

<head>
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="" />
    <link rel="stylesheet" as="style" onload="this.rel='stylesheet'"
        href="https://fonts.googleapis.com/css2?display=swap&family=Noto+Sans+JP:wght@400;500;700&family=Inter:wght@400;500;700" />
    <title>パスワードリセット | mec-portal</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <style>
        /* 日本語フォントを優先するための設定 */
        body {
            font-family: 'Inter', 'Noto Sans JP', sans-serif;
        }
    </style>
</head>

<body>
    <div class="min-h-screen bg-gray-50 flex items-center justify-center">
        <div class="max-w-5xl w-full mx-auto p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 bg-white rounded-2xl shadow-2xl overflow-hidden">

                <!-- 左側：画像エリア -->
                <div class="hidden md:block bg-cover bg-center"
                    style="background-image: url('{{ asset('images/login_background.jpg') }}');">
                </div>

                <!-- 右側：フォームエリア -->
                <div class="p-8 md:p-12">
                    <!-- ロゴ -->
                    <div class="flex items-center gap-3 mb-8">
                        <img src="{{ asset('favicons/favicon.ico') }}" alt="会社のロゴ" class="h-10 w-auto">
                        <h2 class="text-gray-800 text-xl font-bold">mec-portal</h2>
                    </div>

                    <h3 class="text-3xl font-bold text-gray-900">パスワードリセット</h3>
                    <p class="text-gray-500 mt-2 mb-8">
                        パスワードをお忘れの場合、メールアドレスを入力してください。<br>
                        パスワードリセットリンクをメールでお送りします。
                    </p>

                    <!-- Session Status -->
                    @if (session('status'))
                        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form action="{{ route('password.email') }}" method="POST">
                        @csrf
                        <div class="space-y-6">
                            <!-- メールアドレス -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">メールアドレス</label>
                                <div class="mt-1">
                                    <input id="email" name="email" type="email" placeholder="メールアドレスを入力"
                                        class="form-input w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors"
                                        value="{{ old('email') }}" required autofocus />
                                </div>
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- 送信ボタン -->
                            <div>
                                <button type="submit"
                                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                    リセットリンクを送信
                                </button>
                            </div>

                            <!-- ログインに戻るリンク -->
                            <div class="text-center">
                                <a href="{{ route('login') }}"
                                    class="text-sm text-indigo-600 hover:text-indigo-500 hover:underline">
                                    ログインに戻る
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
