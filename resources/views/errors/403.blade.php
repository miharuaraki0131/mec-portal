<x-app-layout>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gray-100">
        <div class="max-w-md w-full bg-white rounded-lg shadow-md p-8 text-center">
            <div class="mb-6">
                <svg class="mx-auto h-24 w-24 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            
            <h1 class="text-3xl font-bold text-gray-900 mb-4">403</h1>
            <h2 class="text-xl font-semibold text-gray-700 mb-4">アクセス権限がありません</h2>
            
            <p class="text-gray-600 mb-8">
                {{ $exception->getMessage() ?: 'このページにアクセスする権限がありません。' }}
            </p>
            
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

