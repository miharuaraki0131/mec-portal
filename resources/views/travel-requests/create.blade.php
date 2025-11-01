<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('出張申請') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('travel-requests.store') }}" id="travelRequestForm">
                        @csrf

                        <!-- 出張先 -->
                        <div class="mb-4">
                            <label for="destination" class="block text-sm font-medium text-gray-700 mb-1">
                                出張先 <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="destination" 
                                   name="destination" 
                                   value="{{ old('destination') }}"
                                   placeholder="例: 東京支社"
                                   required
                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-200 @error('destination') border-red-500 @enderror">
                            @error('destination')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 目的 -->
                        <div class="mb-4">
                            <label for="purpose" class="block text-sm font-medium text-gray-700 mb-1">
                                目的 <span class="text-red-500">*</span>
                            </label>
                            <textarea id="purpose" 
                                      name="purpose" 
                                      rows="3"
                                      placeholder="出張の目的を入力してください"
                                      required
                                      class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-200 @error('purpose') border-red-500 @enderror">{{ old('purpose') }}</textarea>
                            @error('purpose')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 日付 -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="departure_date" class="block text-sm font-medium text-gray-700 mb-1">
                                    出発日 <span class="text-red-500">*</span>
                                </label>
                                <input type="date" 
                                       id="departure_date" 
                                       name="departure_date" 
                                       value="{{ old('departure_date') }}"
                                       required
                                       class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-200 @error('departure_date') border-red-500 @enderror">
                                @error('departure_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="return_date" class="block text-sm font-medium text-gray-700 mb-1">
                                    帰着日 <span class="text-red-500">*</span>
                                </label>
                                <input type="date" 
                                       id="return_date" 
                                       name="return_date" 
                                       value="{{ old('return_date') }}"
                                       required
                                       class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-200 @error('return_date') border-red-500 @enderror">
                                @error('return_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- 前払金 -->
                        <div class="mb-6">
                            <label for="advance_payment" class="block text-sm font-medium text-gray-700 mb-1">
                                前払金（任意）
                            </label>
                            <input type="number" 
                                   id="advance_payment" 
                                   name="advance_payment" 
                                   value="{{ old('advance_payment', 0) }}"
                                   min="0"
                                   step="1"
                                   placeholder="0"
                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-200 @error('advance_payment') border-red-500 @enderror">
                            @error('advance_payment')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 経費明細 -->
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-3">
                                <label class="block text-sm font-medium text-gray-700">
                                    経費明細 <span class="text-red-500">*</span>
                                </label>
                                <button type="button" 
                                        id="addExpenseBtn"
                                        class="text-sm bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded-lg transition-colors">
                                    + 明細を追加
                                </button>
                            </div>
                            <div id="expensesContainer" class="space-y-4">
                                <!-- 初期の明細行（JavaScriptで動的に追加） -->
                                <div class="expense-item border border-gray-200 rounded-lg p-4">
                                    <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">日付</label>
                                            <input type="date" 
                                                   name="expenses[0][date]" 
                                                   required
                                                   class="w-full rounded-lg border-gray-300 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">費目</label>
                                            <select name="expenses[0][category]" 
                                                    required
                                                    class="w-full rounded-lg border-gray-300 text-sm">
                                                <option value="交通費">交通費</option>
                                                <option value="宿泊費">宿泊費</option>
                                                <option value="日当">日当</option>
                                                <option value="半日当">半日当</option>
                                                <option value="その他">その他</option>
                                            </select>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-xs font-medium text-gray-700 mb-1">内容</label>
                                            <input type="text" 
                                                   name="expenses[0][description]" 
                                                   required
                                                   class="w-full rounded-lg border-gray-300 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">現金</label>
                                            <input type="number" 
                                                   name="expenses[0][cash]" 
                                                   value="0"
                                                   min="0"
                                                   step="1"
                                                   class="expense-cash w-full rounded-lg border-gray-300 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">チケット</label>
                                            <input type="number" 
                                                   name="expenses[0][ticket]" 
                                                   value="0"
                                                   min="0"
                                                   step="1"
                                                   class="expense-ticket w-full rounded-lg border-gray-300 text-sm">
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">備考（任意）</label>
                                        <input type="text" 
                                               name="expenses[0][remarks]" 
                                               class="w-full rounded-lg border-gray-300 text-sm">
                                    </div>
                                    <button type="button" 
                                            class="remove-expense-btn mt-2 text-xs text-red-600 hover:text-red-800">
                                        削除
                                    </button>
                                </div>
                            </div>
                            @error('expenses')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
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
        let expenseIndex = 1;

        document.getElementById('addExpenseBtn').addEventListener('click', function() {
            const container = document.getElementById('expensesContainer');
            const template = container.querySelector('.expense-item').cloneNode(true);
            
            // インデックスを更新
            template.querySelectorAll('input, select').forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace(/\[0\]/, `[${expenseIndex}]`));
                }
                if (input.type === 'number') {
                    input.value = '0';
                } else if (input.type === 'date') {
                    input.value = '';
                } else if (input.type === 'text') {
                    input.value = '';
                }
            });
            
            expenseIndex++;
            container.appendChild(template);
        });

        // 削除ボタンのイベント（動的に追加された要素にも対応）
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-expense-btn')) {
                const expenseItems = document.querySelectorAll('.expense-item');
                if (expenseItems.length > 1) {
                    e.target.closest('.expense-item').remove();
                } else {
                    alert('最低1つの明細が必要です。');
                }
            }
        });
    </script>
</x-app-layout>

