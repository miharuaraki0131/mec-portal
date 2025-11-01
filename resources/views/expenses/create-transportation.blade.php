<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('交通費申請') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('expenses.store') }}" id="transportationForm">
                        @csrf
                        <input type="hidden" name="is_transportation" value="1">

                        <!-- 申請期間 -->
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div>
                                <label for="period_from" class="block text-base font-medium text-gray-700 mb-1">
                                    期間（開始日） <span class="text-red-500">*</span>
                                </label>
                                <input type="date" 
                                       id="period_from" 
                                       name="period_from" 
                                       value="{{ old('period_from') }}"
                                       required
                                       class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-200 @error('period_from') border-red-500 @enderror">
                                @error('period_from')
                                    <p class="mt-1 text-base text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="period_to" class="block text-base font-medium text-gray-700 mb-1">
                                    期間（終了日） <span class="text-red-500">*</span>
                                </label>
                                <input type="date" 
                                       id="period_to" 
                                       name="period_to" 
                                       value="{{ old('period_to') }}"
                                       required
                                       class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-200 @error('period_to') border-red-500 @enderror">
                                @error('period_to')
                                    <p class="mt-1 text-base text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- 明細 -->
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-3">
                                <label class="block text-base font-medium text-gray-700">
                                    交通費明細 <span class="text-red-500">*</span>
                                </label>
                                <button type="button" 
                                        id="addItemBtn"
                                        class="text-base bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded-lg transition-colors">
                                    + 明細を追加
                                </button>
                            </div>
                            <div id="itemsContainer" class="space-y-4">
                                <!-- 初期の明細行 -->
                                <div class="item-row border border-gray-200 rounded-lg p-4">
                                    <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-3">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">月/日 <span class="text-red-500">*</span></label>
                                            <input type="date" 
                                                   name="items[0][date]" 
                                                   required
                                                   class="w-full rounded-lg border-gray-300 text-base">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-xs font-medium text-gray-700 mb-1">業務（セ/#）・行き先 <span class="text-red-500">*</span></label>
                                            <input type="text" 
                                                   name="items[0][business]" 
                                                   placeholder="例: 面談（場所：大阪事業所）"
                                                   required
                                                   class="w-full rounded-lg border-gray-300 text-base">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">乗物 <span class="text-red-500">*</span></label>
                                            <input type="text" 
                                                   name="items[0][vehicle]" 
                                                   placeholder="例: 南海電車"
                                                   required
                                                   class="w-full rounded-lg border-gray-300 text-base">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-xs font-medium text-gray-700 mb-1">発 ～ （経由） ～ 着 <span class="text-red-500">*</span></label>
                                            <div class="grid grid-cols-3 gap-1">
                                                <input type="text" 
                                                       name="items[0][route_from]" 
                                                       placeholder="発"
                                                       required
                                                       class="w-full rounded-lg border-gray-300 text-xs">
                                                <input type="text" 
                                                       name="items[0][route_via]" 
                                                       placeholder="経由"
                                                       class="w-full rounded-lg border-gray-300 text-xs">
                                                <input type="text" 
                                                       name="items[0][route_to]" 
                                                       placeholder="着"
                                                       required
                                                       class="w-full rounded-lg border-gray-300 text-xs">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">片道/往復 <span class="text-red-500">*</span></label>
                                            <select name="items[0][transportation_type]" 
                                                    required
                                                    class="w-full rounded-lg border-gray-300 text-base">
                                                <option value="往復" selected>往復</option>
                                                <option value="片道">片道</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">金額 <span class="text-red-500">*</span></label>
                                            <input type="number" 
                                                   name="items[0][amount]" 
                                                   value="0"
                                                   min="0"
                                                   step="1"
                                                   required
                                                   class="item-amount w-full rounded-lg border-gray-300 text-base">
                                        </div>
                                    </div>
                                    <button type="button" 
                                            class="remove-item-btn mt-2 text-xs text-red-600 hover:text-red-800">
                                        削除
                                    </button>
                                </div>
                            </div>
                            @error('items')
                                <p class="mt-1 text-base text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 合計金額表示 -->
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-semibold text-gray-900">合計金額</span>
                                <span id="totalAmount" class="text-2xl font-bold text-indigo-600">¥0</span>
                            </div>
                        </div>

                        <!-- 承認者 -->
                        <div class="mb-6">
                            <label for="approver_type" class="block text-base font-medium text-gray-700 mb-1">
                                承認者 <span class="text-red-500">*</span>
                            </label>
                            <select id="approver_type" 
                                    name="approver_type" 
                                    required
                                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-200 @error('approver_type') border-red-500 @enderror">
                                <option value="">選択してください</option>
                                <option value="business" {{ old('approver_type') === 'business' ? 'selected' : '' }}>業務部</option>
                                @if($divisions->count() > 0)
                                    <optgroup label="部門管理者">
                                        @foreach($divisions as $division)
                                            <option value="manager_{{ $division->id }}" {{ old('approver_type') === 'manager_' . $division->id ? 'selected' : '' }}>
                                                {{ $division->full_name }} - {{ $division->manager->name ?? '' }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            </select>
                            @error('approver_type')
                                <p class="mt-1 text-base text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- ボタン -->
                        <div class="flex justify-end gap-3">
                            <a href="{{ route('expenses.menu') }}" 
                               class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium transition-colors">
                                キャンセル
                            </a>
                            <button type="submit" 
                                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors">
                                申請する
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let itemIndex = 1;

        // 明細を追加
        document.getElementById('addItemBtn').addEventListener('click', function() {
            const container = document.getElementById('itemsContainer');
            const template = container.querySelector('.item-row').cloneNode(true);
            
            // インデックスを更新
            template.querySelectorAll('input, select').forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace(/\[0\]/, `[${itemIndex}]`));
                }
                if (input.type === 'number') {
                    input.value = '0';
                } else if (input.type === 'date') {
                    input.value = '';
                } else if (input.type === 'text' && !input.name.includes('route')) {
                    input.value = '';
                }
            });
            
            itemIndex++;
            container.appendChild(template);
            updateTotalAmount();
        });

        // 削除ボタンのイベント
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-item-btn')) {
                const items = document.querySelectorAll('.item-row');
                if (items.length > 1) {
                    e.target.closest('.item-row').remove();
                    updateTotalAmount();
                } else {
                    alert('最低1つの明細が必要です。');
                }
            }
        });

        // 合計金額を計算
        function updateTotalAmount() {
            const amounts = document.querySelectorAll('.item-amount');
            let total = 0;
            amounts.forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            document.getElementById('totalAmount').textContent = '¥' + total.toLocaleString();
        }

        // 金額入力時に合計を更新
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('item-amount')) {
                updateTotalAmount();
            }
        });

        // 初期表示時に合計を計算
        updateTotalAmount();
    </script>
</x-app-layout>

