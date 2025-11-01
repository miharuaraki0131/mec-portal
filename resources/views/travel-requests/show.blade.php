<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('出張申請詳細') }}
            </h2>
            <a href="{{ route('travel-requests.index') }}" 
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- ステータス -->
                    <div class="flex items-center gap-3 mb-4">
                        <span class="bg-{{ $travelRequest->status_color }}-100 text-{{ $travelRequest->status_color }}-800 text-xs font-semibold px-3 py-1 rounded">
                            {{ $travelRequest->status_label }}
                        </span>
                    </div>

                    <!-- 申請者情報 -->
                    <div class="mb-6 pb-4 border-b">
                        <h3 class="text-base font-medium text-gray-500 mb-2">申請者情報</h3>
                        <div class="grid grid-cols-2 gap-4 text-base">
                            <div>
                                <span class="text-gray-500">申請者</span>
                                <p class="text-gray-900 font-medium">{{ $travelRequest->user->name }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">社員コード</span>
                                <p class="text-gray-900 font-medium">{{ $travelRequest->user->user_code }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">所属</span>
                                <p class="text-gray-900 font-medium">{{ $travelRequest->user->division ? $travelRequest->user->division->full_name : '未所属' }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">申請日</span>
                                <p class="text-gray-900 font-medium">{{ $travelRequest->created_at->format('Y年m月d日 H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- 出張情報 -->
                    <div class="mb-6 pb-4 border-b">
                        <h3 class="text-base font-medium text-gray-500 mb-4">出張情報</h3>
                        <div class="grid grid-cols-2 gap-4 text-base">
                            <div>
                                <span class="text-gray-500">出張先</span>
                                <p class="text-gray-900 font-medium">{{ $travelRequest->destination }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">目的</span>
                                <p class="text-gray-900 font-medium">{{ $travelRequest->purpose }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">出発日</span>
                                <p class="text-gray-900 font-medium">{{ $travelRequest->departure_date->format('Y年m月d日') }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">帰着日</span>
                                <p class="text-gray-900 font-medium">{{ $travelRequest->return_date->format('Y年m月d日') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- 経費明細 -->
                    <div class="mb-6 pb-4 border-b">
                        <h3 class="text-base font-medium text-gray-500 mb-4">経費明細</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">日付</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">費目</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">内容</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">現金</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">チケット</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">合計</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($travelRequest->travelExpenses as $expense)
                                        <tr>
                                            <td class="px-4 py-3 whitespace-nowrap text-base text-gray-900">
                                                {{ $expense->date->format('Y/m/d') }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-base text-gray-900">
                                                {{ $expense->category }}
                                            </td>
                                            <td class="px-4 py-3 text-base text-gray-900">
                                                {{ $expense->description }}
                                                @if($expense->remarks)
                                                    <br><span class="text-xs text-gray-500">備考: {{ $expense->remarks }}</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-base text-gray-900 text-right">
                                                {{ number_format($expense->cash) }}円
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-base text-gray-900 text-right">
                                                {{ number_format($expense->ticket) }}円
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-base text-gray-900 font-medium text-right">
                                                {{ number_format($expense->total) }}円
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="3" class="px-4 py-3 text-base font-medium text-gray-900">小計</td>
                                        <td colspan="3" class="px-4 py-3 text-base font-medium text-gray-900 text-right">
                                            {{ number_format($travelRequest->subtotal) }}円
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="px-4 py-3 text-base font-medium text-gray-900">前払金</td>
                                        <td colspan="3" class="px-4 py-3 text-base font-medium text-gray-900 text-right">
                                            {{ number_format($travelRequest->advance_payment) }}円
                                        </td>
                                    </tr>
                                    <tr class="bg-indigo-50">
                                        <td colspan="3" class="px-4 py-3 text-base font-bold text-gray-900">精算金額（{{ $travelRequest->settlement_type_label }}）</td>
                                        <td colspan="3" class="px-4 py-3 text-base font-bold text-gray-900 text-right">
                                            {{ number_format(abs($travelRequest->settlement_amount)) }}円
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- 承認履歴 -->
                    @if($travelRequest->workflowApprovals && $travelRequest->workflowApprovals->count() > 0)
                        <div class="mb-6 pb-4 border-b">
                            <h3 class="text-base font-medium text-gray-500 mb-4">承認履歴</h3>
                            <div class="space-y-3">
                                @foreach($travelRequest->workflowApprovals as $approval)
                                    <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="text-base font-medium text-gray-900">
                                                    {{ $approval->approval_order === 1 ? '部署責任者' : '業務部' }}: {{ $approval->approver->name ?? '未設定' }}
                                                </span>
                                                <span class="bg-{{ $approval->status_color }}-100 text-{{ $approval->status_color }}-800 text-xs font-semibold px-2 py-1 rounded">
                                                    {{ $approval->status_label }}
                                                </span>
                                                @if($approval->is_final_approval)
                                                    <span class="bg-purple-100 text-purple-800 text-xs font-semibold px-2 py-1 rounded">最終承認</span>
                                                @endif
                                            </div>
                                            @if($approval->comment)
                                                <p class="text-base text-gray-600">{{ $approval->comment }}</p>
                                            @endif
                                            @if($approval->approved_at)
                                                <p class="text-xs text-gray-500 mt-1">{{ $approval->approved_at->format('Y年m月d日 H:i') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- アクションボタン -->
                    <div class="flex gap-3 pt-4 border-t">
                        @can('view', $travelRequest)
                            <a href="{{ route('travel-requests.download-excel', $travelRequest->id) }}" 
                               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-base font-medium transition-colors flex items-center gap-2">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Excelをダウンロード
                            </a>
                        @endcan
                        @can('update', $travelRequest)
                            <a href="{{ route('travel-requests.edit', $travelRequest->id) }}" 
                               class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-base font-medium transition-colors">
                                編集
                            </a>
                            <form method="POST" action="{{ route('travel-requests.destroy', $travelRequest->id) }}" 
                                  onsubmit="return confirm('この出張申請を削除しますか？');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-base font-medium transition-colors">
                                    削除
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

