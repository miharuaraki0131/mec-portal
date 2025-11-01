<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('承認待ち一覧') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- フィルター -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4">
                    <form method="GET" action="{{ route('approvals.index') }}" class="flex gap-4">
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-base font-medium text-gray-700 mb-1">申請種別</label>
                            <select name="type" 
                                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-200">
                                <option value="">すべて</option>
                                <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>経費申請</option>
                                <option value="travel" {{ request('type') === 'travel' ? 'selected' : '' }}>出張申請</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="submit" 
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                                絞り込み
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @if($approvals->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">申請種別</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">申請者</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">内容</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">金額</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">申請日</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">操作</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($approvals as $approval)
                                    @php
                                        if ($approval->request_type === 'expense') {
                                            $request = \App\Models\Expense::find($approval->request_id);
                                            $title = $request ? ($request->isTransportation() ? '交通費申請（' . $request->period_from->format('Y/m/d') . '～' . $request->period_to->format('Y/m/d') . '）' : $request->category) : '削除済み';
                                            $amount = $request ? ($request->isTransportation() ? number_format($request->total_amount) : number_format($request->amount)) : '0';
                                        } else {
                                            $request = \App\Models\TravelRequest::find($approval->request_id);
                                            $title = $request ? $request->destination : '削除済み';
                                            $amount = $request ? number_format($request->settlement_amount) : '0';
                                        }
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-base text-gray-900">
                                            @if($approval->request_type === 'expense')
                                                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-1 rounded">経費</span>
                                            @else
                                                <span class="bg-orange-100 text-orange-800 text-xs font-semibold px-2 py-1 rounded">出張</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-base text-gray-900">
                                            {{ $approval->applicant->name }} ({{ $approval->applicant->user_code }})
                                        </td>
                                        <td class="px-6 py-4 text-base text-gray-900">
                                            {{ $title }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-base text-gray-900">
                                            {{ $amount }}円
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-base text-gray-500">
                                            {{ $approval->created_at->format('Y/m/d') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-base font-medium">
                                            <div class="flex justify-end gap-2">
                                                <a href="{{ $approval->request_type === 'expense' ? route('expenses.download-excel', $approval->request_id) : route('travel-requests.download-excel', $approval->request_id) }}" 
                                                   class="text-blue-600 hover:text-blue-900"
                                                   title="Excelをダウンロード">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                </a>
                                                <a href="{{ $approval->request_type === 'expense' ? route('expenses.show', $approval->request_id) : route('travel-requests.show', $approval->request_id) }}" 
                                                   class="text-indigo-600 hover:text-indigo-900">詳細</a>
                                                <button onclick="openApproveModal({{ $approval->id }})" 
                                                        class="text-green-600 hover:text-green-900">承認</button>
                                                <button onclick="openRejectModal({{ $approval->id }})" 
                                                        class="text-red-600 hover:text-red-900">差戻</button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ページネーション -->
                <div class="mt-4">
                    {{ $approvals->links() }}
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center text-gray-500">
                        承認待ちの申請はありません。
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- 承認モーダル -->
    <div id="approveModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <h3 class="text-lg font-bold mb-4">承認</h3>
            <form id="approveForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-base font-medium text-gray-700 mb-1">コメント（任意）</label>
                    <textarea name="comment" rows="3" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-200"></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeApproveModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium transition-colors">
                        キャンセル
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors">
                        承認する
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 差戻モーダル -->
    <div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <h3 class="text-lg font-bold mb-4">差戻</h3>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-base font-medium text-gray-700 mb-1">差戻理由 <span class="text-red-500">*</span></label>
                    <textarea name="comment" rows="3" required class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-200"></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium transition-colors">
                        キャンセル
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                        差戻す
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openApproveModal(approvalId) {
            const form = document.getElementById('approveForm');
            form.action = `/approvals/${approvalId}/approve`;
            document.getElementById('approveModal').classList.remove('hidden');
        }

        function closeApproveModal() {
            document.getElementById('approveModal').classList.add('hidden');
        }

        function openRejectModal(approvalId) {
            const form = document.getElementById('rejectForm');
            form.action = `/approvals/${approvalId}/reject`;
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }

        // モーダル外クリックで閉じる
        window.onclick = function(event) {
            const approveModal = document.getElementById('approveModal');
            const rejectModal = document.getElementById('rejectModal');
            if (event.target === approveModal) {
                closeApproveModal();
            }
            if (event.target === rejectModal) {
                closeRejectModal();
            }
        }
    </script>
</x-app-layout>

