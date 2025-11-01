<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>出張申請通知</title>
</head>
<body style="font-family: 'Hiragino Sans', 'ヒラギノ角ゴシック', 'Yu Gothic', '游ゴシック', Meiryo, 'メイリオ', sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="color: #4F46E5; border-bottom: 2px solid #4F46E5; padding-bottom: 10px;">出張申請通知</h1>
        
        <p style="margin-top: 20px;">新しい出張申請が届きました。</p>
        
        <div style="background-color: #F3F4F6; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <h2 style="margin-top: 0; color: #1F2937;">申請内容</h2>
            
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; width: 120px; font-weight: bold;">申請者:</td>
                    <td style="padding: 8px 0;">{{ $applicant->name }} ({{ $applicant->user_code }})</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">所属:</td>
                    <td style="padding: 8px 0;">{{ $applicant->division ? $applicant->division->full_name : '未所属' }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">出張先:</td>
                    <td style="padding: 8px 0;">{{ $travelRequest->destination }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">目的:</td>
                    <td style="padding: 8px 0;">{{ $travelRequest->purpose }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">出発日:</td>
                    <td style="padding: 8px 0;">{{ $travelRequest->departure_date->format('Y年m月d日') }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">帰着日:</td>
                    <td style="padding: 8px 0;">{{ $travelRequest->return_date->format('Y年m月d日') }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">精算金額:</td>
                    <td style="padding: 8px 0; font-size: 18px; font-weight: bold; color: #4F46E5;">
                        {{ number_format(abs($travelRequest->settlement_amount)) }}円（{{ $travelRequest->settlement_type_label }}）
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">申請日時:</td>
                    <td style="padding: 8px 0;">{{ $travelRequest->created_at->format('Y年m月d日 H:i') }}</td>
                </tr>
            </table>
        </div>
        
        <p style="margin-top: 20px;">詳細なExcelファイルはポータルサイトの承認待ち一覧からダウンロードしてください。</p>
        
        <p style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #E5E7EB; color: #6B7280; font-size: 12px;">
            このメールは自動送信されています。<br>
            mec-portal システムより
        </p>
    </div>
</body>
</html>

