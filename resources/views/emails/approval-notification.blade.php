<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>承認通知</title>
</head>
<body style="font-family: 'Hiragino Sans', 'ヒラギノ角ゴシック', 'Yu Gothic', '游ゴシック', Meiryo, 'メイリオ', sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="color: #059669; border-bottom: 2px solid #059669; padding-bottom: 10px;">承認通知</h1>
        
        <p style="margin-top: 20px;">申請が承認されました。</p>
        
        <div style="background-color: #F3F4F6; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <h2 style="margin-top: 0; color: #1F2937;">申請内容</h2>
            
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; width: 120px; font-weight: bold;">申請種別:</td>
                    <td style="padding: 8px 0;">{{ $approval->request_type === 'expense' ? '経費申請' : '出張申請' }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">承認者:</td>
                    <td style="padding: 8px 0;">{{ $approval->approver->name }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">承認日時:</td>
                    <td style="padding: 8px 0;">{{ $approval->approved_at->format('Y年m月d日 H:i') }}</td>
                </tr>
                @if($approval->comment)
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold;">コメント:</td>
                        <td style="padding: 8px 0;">{{ $approval->comment }}</td>
                    </tr>
                @endif
            </table>
        </div>
        
        <p style="margin-top: 20px;">詳細はポータルサイトでご確認ください。</p>
        
        <p style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #E5E7EB; color: #6B7280; font-size: 12px;">
            このメールは自動送信されています。<br>
            mec-portal システムより
        </p>
    </div>
</body>
</html>

