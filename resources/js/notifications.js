// ブラウザ通知の管理
export class BrowserNotification {
    static async requestPermission() {
        if (!('Notification' in window)) {
            console.warn('このブラウザは通知をサポートしていません');
            return false;
        }

        if (Notification.permission === 'granted') {
            return true;
        }

        if (Notification.permission !== 'denied') {
            const permission = await Notification.requestPermission();
            return permission === 'granted';
        }

        return false;
    }

    static async show(title, options = {}) {
        const hasPermission = await this.requestPermission();
        if (!hasPermission) {
            return null;
        }

        const defaultOptions = {
            icon: '/favicons/favicon.ico',
            badge: '/favicons/favicon.ico',
            tag: 'mec-portal',
            requireInteraction: false,
            ...options,
        };

        return new Notification(title, defaultOptions);
    }

    static async checkPendingApprovals() {
        try {
            const response = await fetch('/api/pending-approvals-count');
            if (!response.ok) return;

            const data = await response.json();
            const count = data.count || 0;

            // 前回のカウントを取得
            const lastCount = localStorage.getItem('lastApprovalCount') || '0';
            const lastCountInt = parseInt(lastCount);

            // カウントが増えた場合のみ通知
            if (count > 0 && count > lastCountInt) {
                const title = '承認待ちがあります';
                const message = `${count}件の承認待ち申請があります`;

                await this.show(title, {
                    body: message,
                    tag: 'pending-approvals',
                    requireInteraction: true,
                });
            }

            localStorage.setItem('lastApprovalCount', count.toString());
        } catch (error) {
            console.error('承認待ちチェックエラー:', error);
        }
    }
}

// グローバルに公開
window.BrowserNotification = BrowserNotification;

// ページロード時に通知権限をリクエスト
document.addEventListener('DOMContentLoaded', async function() {
    await BrowserNotification.requestPermission();

    // 5分ごとに承認待ちをチェック
    if (window.location.pathname === '/dashboard') {
        BrowserNotification.checkPendingApprovals();
        setInterval(() => {
            BrowserNotification.checkPendingApprovals();
        }, 5 * 60 * 1000); // 5分
    }
});

