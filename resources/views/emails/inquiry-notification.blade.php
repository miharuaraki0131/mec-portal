<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ご意見箱からの問い合わせ</title>
</head>
<body style="font-family: 'Hiragino Kaku Gothic ProN', 'Hiragino Sans', Meiryo, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #4f46e5; border-bottom: 2px solid #4f46e5; padding-bottom: 10px;">
            ご意見箱から問い合わせが届きました
        </h2>
        
        <div style="background-color: #f9fafb; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p style="margin: 0;"><strong>件名:</strong> {{ $inquiry->subject }}</p>
            <p style="margin: 5px 0;"><strong>送信先部署:</strong> {{ $inquiry->department }}</p>
            <p style="margin: 5px 0;"><strong>送信者:</strong> {{ $sender->name }} ({{ $sender->email }})</p>
            <p style="margin: 5px 0;"><strong>送信日時:</strong> {{ $inquiry->created_at->format('Y年m月d日 H:i') }}</p>
        </div>

        <div style="background-color: #ffffff; border: 1px solid #e5e7eb; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <h3 style="color: #374151; margin-top: 0;">メッセージ内容</h3>
            <div style="white-space: pre-wrap; color: #4b5563;">{{ $inquiry->message }}</div>
        </div>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
            <p style="color: #6b7280; font-size: 14px;">
                このメールは、mec-portalのご意見箱から自動送信されました。<br>
                システムへの返信はできませんので、システム内で対応してください。
            </p>
        </div>
    </div>
</body>
</html>

