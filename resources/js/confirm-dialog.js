// 統一された確認ダイアログ
export class ConfirmDialog {
    static show(options = {}) {
        const {
            title = '確認',
            message = 'この操作を実行してもよろしいですか？',
            confirmText = 'はい',
            cancelText = 'いいえ',
            confirmClass = 'bg-red-600 hover:bg-red-700',
        } = options;

        return new Promise((resolve) => {
            // 既存のダイアログを削除
            const existing = document.getElementById('confirm-dialog');
            if (existing) {
                existing.remove();
            }

            // ダイアログ要素を作成
            const overlay = document.createElement('div');
            overlay.id = 'confirm-dialog';
            overlay.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center';
            overlay.innerHTML = `
                <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">${title}</h3>
                        <p class="text-base text-gray-700 mb-6">${message}</p>
                        <div class="flex justify-end space-x-3">
                            <button id="confirm-cancel" class="px-4 py-2 text-base font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                ${cancelText}
                            </button>
                            <button id="confirm-ok" class="px-4 py-2 text-base font-medium text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 ${confirmClass}">
                                ${confirmText}
                            </button>
                        </div>
                    </div>
                </div>
            `;

            // イベントリスナー
            const cancelBtn = overlay.querySelector('#confirm-cancel');
            const okBtn = overlay.querySelector('#confirm-ok');

            cancelBtn.addEventListener('click', () => {
                overlay.remove();
                resolve(false);
            });

            okBtn.addEventListener('click', () => {
                overlay.remove();
                resolve(true);
            });

            // オーバーレイクリックで閉じる
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) {
                    overlay.remove();
                    resolve(false);
                }
            });

            // ESCキーで閉じる
            const handleEsc = (e) => {
                if (e.key === 'Escape') {
                    overlay.remove();
                    document.removeEventListener('keydown', handleEsc);
                    resolve(false);
                }
            };
            document.addEventListener('keydown', handleEsc);

            document.body.appendChild(overlay);
        });
    }
}

// グローバルに公開
window.ConfirmDialog = ConfirmDialog;

// 削除ボタンに自動的に確認ダイアログを追加
document.addEventListener('DOMContentLoaded', function() {
    // data-confirm属性がある削除フォームに確認ダイアログを追加
    document.querySelectorAll('form[data-confirm]').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const message = form.dataset.confirm || 'この操作を実行してもよろしいですか？';
            const title = form.dataset.confirmTitle || '確認';

            ConfirmDialog.show({
                title: title,
                message: message,
                confirmText: '削除',
                cancelText: 'キャンセル',
            }).then(confirmed => {
                if (confirmed) {
                    // ローディング表示をスキップ（フォーム自体で処理）
                    form.dataset.skipLoading = 'true';
                    form.submit();
                }
            });
        });
    });

    // data-confirm属性があるリンクにも確認ダイアログを追加
    document.querySelectorAll('a[data-confirm]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();

            const message = link.dataset.confirm || 'この操作を実行してもよろしいですか？';
            const title = link.dataset.confirmTitle || '確認';
            const href = link.href;

            ConfirmDialog.show({
                title: title,
                message: message,
            }).then(confirmed => {
                if (confirmed) {
                    window.location.href = href;
                }
            });
        });
    });
});

