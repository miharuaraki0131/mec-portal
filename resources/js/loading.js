// ローディング表示のユーティリティ
export class LoadingIndicator {
    constructor() {
        this.createOverlay();
    }

    createOverlay() {
        if (document.getElementById('loading-overlay')) {
            return;
        }

        const overlay = document.createElement('div');
        overlay.id = 'loading-overlay';
        overlay.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden';
        overlay.innerHTML = `
            <div class="bg-white rounded-lg p-6 flex flex-col items-center space-y-4">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
                <p class="text-base text-gray-700">処理中...</p>
            </div>
        `;
        document.body.appendChild(overlay);
    }

    show(message = '処理中...') {
        const overlay = document.getElementById('loading-overlay');
        const messageElement = overlay.querySelector('p');
        if (messageElement) {
            messageElement.textContent = message;
        }
        overlay.classList.remove('hidden');
    }

    hide() {
        const overlay = document.getElementById('loading-overlay');
        overlay.classList.add('hidden');
    }
}

// グローバルインスタンス
window.loadingIndicator = new LoadingIndicator();

// フォーム送信時の自動ローディング表示
document.addEventListener('DOMContentLoaded', function() {
    // すべてのフォームにローディング表示を追加
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            // 削除ボタンなどの確認ダイアログがある場合はスキップ
            if (form.dataset.skipLoading === 'true') {
                return;
            }

            // ファイルアップロードフォームの場合は表示
            if (form.querySelector('input[type="file"]') || form.enctype === 'multipart/form-data') {
                window.loadingIndicator.show('アップロード中...');
            } else {
                window.loadingIndicator.show();
            }
        });
    });

    // Axiosリクエスト時の自動ローディング表示
    if (window.axios) {
        let activeRequests = 0;

        // リクエスト開始
        window.axios.interceptors.request.use(config => {
            activeRequests++;
            if (activeRequests === 1) {
                window.loadingIndicator.show();
            }
            return config;
        });

        // レスポンス受信（成功/失敗問わず）
        window.axios.interceptors.response.use(
            response => {
                activeRequests--;
                if (activeRequests === 0) {
                    window.loadingIndicator.hide();
                }
                return response;
            },
            error => {
                activeRequests--;
                if (activeRequests === 0) {
                    window.loadingIndicator.hide();
                }
                return Promise.reject(error);
            }
        );
    }
});

