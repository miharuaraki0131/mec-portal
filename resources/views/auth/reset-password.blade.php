<!DOCTYPE html>
<html>

<head>
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="" />
    <link rel="stylesheet" as="style" onload="this.rel='stylesheet'"
        href="https://fonts.googleapis.com/css2?display=swap&family=Noto+Sans+JP:wght@400;500;700&family=Inter:wght@400;500;700" />
    <title>パスワード再設定 | mec-portal</title>
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

                    <h3 class="text-3xl font-bold text-gray-900">パスワード再設定</h3>
                    <p class="text-gray-500 mt-2 mb-8">新しいパスワードを入力してください。</p>

                    <form action="{{ route('password.store') }}" method="POST">
                        @csrf

                        <!-- Password Reset Token -->
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                        <div class="space-y-6">
                            <!-- メールアドレス -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">メールアドレス</label>
                                <div class="mt-1">
                                    <input id="email" name="email" type="email" placeholder="メールアドレスを入力"
                                        class="form-input w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors"
                                        value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" />
                                </div>
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- パスワード -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">新しいパスワード</label>
                                <div class="mt-1">
                                    <input id="password" name="password" type="password" placeholder="新しいパスワードを入力"
                                        class="form-input w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors"
                                        required autocomplete="new-password" />
                                </div>
                                @error('password')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- パスワード確認 -->
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">パスワード確認</label>
                                <div class="mt-1">
                                    <input id="password_confirmation" name="password_confirmation" type="password"
                                        placeholder="パスワードを再度入力"
                                        class="form-input w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors"
                                        required autocomplete="new-password" />
                                </div>
                                @error('password_confirmation')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- 送信ボタン -->
                            <div>
                                <button type="submit"
                                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                    パスワードをリセット
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
