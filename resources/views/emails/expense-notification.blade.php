<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>経費申請の通知</title>
</head>
<body style="font-family: 'Hiragino Kaku Gothic ProN', 'Hiragino Sans', Meiryo, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #4f46e5; border-bottom: 2px solid #4f46e5; padding-bottom: 10px;">
            経費申請が届きました
        </h2>
        
        <div style="background-color: #f9fafb; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p style="margin: 0;"><strong>申請者:</strong> {{ $applicant->name }} ({{ $applicant->user_code }})</p>
            <p style="margin: 5px 0;"><strong>所属:</strong> {{ $applicant->division ? $applicant->division->full_name : '未所属' }}</p>
            <p style="margin: 5px 0;"><strong>申請日:</strong> {{ $expense->created_at->format('Y年m月d日 H:i') }}</p>
        </div>

        <div style="background-color: #ffffff; border: 1px solid #e5e7eb; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <h3 style="color: #374151; margin-top: 0;">経費明細</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #e5e7eb;"><strong>日付</strong></td>
                    <td style="padding: 8px; border-bottom: 1px solid #e5e7eb;">{{ $expense->date->format('Y年m月d日') }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #e5e7eb;"><strong>費目</strong></td>
                    <td style="padding: 8px; border-bottom: 1px solid #e5e7eb;">{{ $expense->category }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #e5e7eb;"><strong>内容</strong></td>
                    <td style="padding: 8px; border-bottom: 1px solid #e5e7eb;">{{ $expense->description }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px;"><strong>金額</strong></td>
                    <td style="padding: 8px;"><strong>{{ number_format($expense->amount) }}円</strong></td>
                </tr>
            </table>
        </div>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
            <p style="color: #6b7280; font-size: 14px;">
                このメールは、mec-portalの経費精算システムから自動送信されました。<br>
                詳細は添付のExcelファイルをご確認ください。
            </p>
        </div>
    </div>
</body>
</html>

