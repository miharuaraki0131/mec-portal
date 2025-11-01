<x-app-layout>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gray-100">
        <div class="max-w-md w-full bg-white rounded-lg shadow-md p-8 text-center">
            <div class="mb-6">
                <svg class="mx-auto h-24 w-24 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            
            <h1 class="text-3xl font-bold text-gray-900 mb-4">500</h1>
            <h2 class="text-xl font-semibold text-gray-700 mb-4">サーバーエラー</h2>
            
            <p class="text-gray-600 mb-8">
                サーバーでエラーが発生しました。しばらく時間をおいてから再度お試しください。
            </p>
            
            @if(config('app.debug'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded text-left">
                    <p class="text-base text-red-800 font-mono break-all">
                        {{ $exception->getMessage() }}
                    </p>
                </div>
            @endif
            
            <div class="space-y-3">
                <a href="{{ route('dashboard') }}" 
                   class="block w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                    ダッシュボードに戻る
                </a>
                <a href="{{ url()->previous() }}" 
                   class="block w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-lg transition-colors">
                    前のページに戻る
                </a>
            </div>
        </div>
    </div>
</x-app-layout>

