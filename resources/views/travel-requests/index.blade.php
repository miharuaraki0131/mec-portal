<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('出張申請一覧') }}
            </h2>
            <a href="{{ route('travel-requests.create') }}" 
               class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-base font-medium transition-colors">
                新規申請
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- フィルター -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4">
                    <form method="GET" action="{{ route('travel-requests.index') }}" class="flex gap-4">
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-base font-medium text-gray-700 mb-1">ステータス</label>
                            <select name="status" 
                                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-200">
                                <option value="">すべて</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>申請中</option>
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>承認済</option>
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

            @if($travelRequests->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">出張先</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">出発日</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">帰着日</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">精算金額</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ステータス</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">申請日</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">操作</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($travelRequests as $travelRequest)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-base text-gray-900">
                                            {{ $travelRequest->destination }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-base text-gray-900">
                                            {{ $travelRequest->departure_date->format('Y/m/d') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-base text-gray-900">
                                            {{ $travelRequest->return_date->format('Y/m/d') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-base text-gray-900">
                                            {{ number_format($travelRequest->settlement_amount) }}円
                                            <span class="text-gray-500 text-xs">({{ $travelRequest->settlement_type_label }})</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="bg-{{ $travelRequest->status_color }}-100 text-{{ $travelRequest->status_color }}-800 text-xs font-semibold px-2 py-1 rounded">
                                                {{ $travelRequest->status_label }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-base text-gray-500">
                                            {{ $travelRequest->created_at->format('Y/m/d') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-base font-medium">
                                            <a href="{{ route('travel-requests.show', $travelRequest->id) }}" 
                                               class="text-indigo-600 hover:text-indigo-900">詳細</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ページネーション -->
                <div class="mt-4">
                    {{ $travelRequests->links() }}
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center text-gray-500">
                        出張申請はありません。
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

