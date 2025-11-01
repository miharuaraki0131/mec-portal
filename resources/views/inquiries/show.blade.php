<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('問い合わせ詳細') }}
            </h2>
            <a href="{{ route('inquiries.index') }}" 
               class="text-indigo-600 hover:text-indigo-800 text-base font-medium">
                ← 一覧に戻る
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- スレッド表示 -->
            <div class="space-y-4">
                @foreach($threadMessages as $message)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg {{ $message->id === $inquiry->id ? 'border-2 border-indigo-500' : '' }}">
                        <div class="p-6">
                            <!-- ヘッダー -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <span class="bg-{{ $message->status_color }}-100 text-{{ $message->status_color }}-800 text-xs font-semibold px-2 py-1 rounded">
                                        {{ $message->status_label }}
                                    </span>
                                    <span class="bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded">
                                        {{ $message->department }}
                                    </span>
                                    @if($message->parent_id)
                                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                                            返信
                                        </span>
                                    @endif
                                </div>
                                @can('updateStatus', $message)
                                    <form method="POST" action="{{ route('inquiries.updateStatus', $message->id) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" 
                                                onchange="this.form.submit()"
                                                class="text-xs rounded border-gray-300 focus:border-indigo-500">
                                            <option value="0" {{ $message->status === 0 ? 'selected' : '' }}>未対応</option>
                                            <option value="1" {{ $message->status === 1 ? 'selected' : '' }}>対応中</option>
                                            <option value="2" {{ $message->status === 2 ? 'selected' : '' }}>対応済</option>
                                        </select>
                                    </form>
                                @endcan
                            </div>

                            <!-- 件名 -->
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">
                                {{ $message->subject }}
                            </h3>

                            <!-- メタ情報 -->
                            <div class="flex items-center gap-4 text-base text-gray-500 mb-4 pb-4 border-b">
                                <span>送信者: {{ $message->user->name ?? '不明' }}</span>
                                <span>送信日: {{ $message->created_at->format('Y年m月d日 H:i') }}</span>
                                @if($message->replied_by)
                                    <span>返信者: {{ $message->repliedBy->name ?? '不明' }}</span>
                                    <span>返信日: {{ $message->replied_at ? $message->replied_at->format('Y年m月d日 H:i') : '' }}</span>
                                @endif
                            </div>

                            <!-- メッセージ -->
                            <div class="prose max-w-none mb-4">
                                <div class="text-gray-700 whitespace-pre-wrap">
                                    {{ $message->message }}
                                </div>
                            </div>

                            <!-- アクションボタン（元の問い合わせのみ表示） -->
                            @if($message->id === $inquiry->id)
                                <div class="flex gap-3 pt-4 border-t">
                                    @can('update', $message)
                                        <a href="{{ route('inquiries.edit', $message->id) }}" 
                                           class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-base font-medium transition-colors">
                                            編集
                                        </a>
                                    @endcan
                                    @can('delete', $message)
                                        <form method="POST" action="{{ route('inquiries.destroy', $message->id) }}" 
                                              onsubmit="return confirm('この問い合わせを削除しますか？');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-base font-medium transition-colors">
                                                削除
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- 返信フォーム -->
            @can('reply', $inquiry)
                <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">返信する</h3>
                        <form method="POST" action="{{ route('inquiries.reply', $inquiry->id) }}">
                            @csrf

                            <div class="mb-4">
                                <label for="reply_message" class="block text-base font-medium text-gray-700 mb-1">
                                    返信メッセージ <span class="text-red-500">*</span>
                                </label>
                                <textarea id="reply_message" 
                                          name="message" 
                                          rows="6"
                                          required
                                          class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-200 @error('message') border-red-500 @enderror">{{ old('message') }}</textarea>
                                @error('message')
                                    <p class="mt-1 text-base text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" 
                                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                                    返信を送信
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endcan
        </div>
    </div>
</x-app-layout>

