<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseRequest;
use App\Http\Requests\TransportationExpenseRequest;
use App\Models\Expense;
use App\Models\Division;
use App\Models\User;
use App\Models\WorkflowApproval;
use App\Mail\ExpenseNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // 親レコード（交通費申請を含む）と通常の経費申請のみ取得（明細は除外）
        $query = Expense::with(['user', 'children'])
            ->where('user_id', Auth::id())
            ->whereNull('parent_id') // 親レコードまたは通常の経費のみ
            ->latest('date');

        // ステータスでフィルタリング
        if ($request->has('status')) {
            match($request->status) {
                'pending' => $query->pending(),
                'approved' => $query->approved(),
                default => null,
            };
        }

        $expenses = $query->paginate(15);

        return view('expenses.index', compact('expenses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // 交通費申請の場合、専用フォームを表示
        if ($request->has('type') && $request->type === 'transportation') {
            return view('expenses.create-transportation');
        }

        // 申請タイプに応じて費目を初期設定
        $category = '';
        if ($request->has('type')) {
            $category = match($request->type) {
                'transportation' => '交通費',
                'expense' => '',
                default => '',
            };
        }

        return view('expenses.create', compact('category'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 交通費申請の場合
        if ($request->has('is_transportation') && $request->is_transportation) {
            $validatedRequest = TransportationExpenseRequest::createFrom($request);
            $validatedRequest->validateResolved();
            return $this->storeTransportation($validatedRequest);
        }

        // 通常の経費申請
        $validatedRequest = ExpenseRequest::createFrom($request);
        $validatedRequest->validateResolved();
        $validated = $validatedRequest->validated();

        // トランザクション処理
        DB::transaction(function () use ($validated, $request, &$expense) {
            // 領収書のアップロード
            $receiptPath = null;
            if ($request->hasFile('receipt')) {
                $file = $request->file('receipt');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $receiptPath = $file->storeAs('receipts', $fileName, 'public');
            }

            $expense = Expense::create([
                'user_id' => Auth::id(),
                'date' => $validated['date'],
                'category' => $validated['category'],
                'amount' => $validated['amount'],
                'description' => $validated['description'],
                'receipt_path' => $receiptPath,
                'status' => Expense::STATUS_PENDING,
            ]);

            // 承認フローを作成（業務部）
            $this->createExpenseApprovalFlow($expense);
        });

        // トランザクション外でExcel生成とメール送信（ファイル操作は例外時も処理完了させたい）
        try {
            $this->generateExcelAndSendEmail($expense);
        } catch (\Exception $e) {
            \Log::error('Excel生成・メール送信エラー: ' . $e->getMessage());
            return redirect()->route('expenses.index')
                ->with('warning', '経費申請は登録されましたが、メール送信でエラーが発生しました。');
        }

        return redirect()->route('expenses.index')
            ->with('success', '経費申請を送信しました。業務部に通知を送信しました。');
    }

    /**
     * 交通費申請を保存
     */
    private function storeTransportation(TransportationExpenseRequest $request)
    {
        $validated = $request->validated();

        // トランザクション処理
        DB::transaction(function () use ($validated, &$parentExpense) {
            // 親レコード（交通費申請）を作成
            $parentExpense = Expense::create([
                'user_id' => Auth::id(),
                'parent_id' => null,
                'category' => '交通費',
                'is_transportation' => true,
                'period_from' => $validated['period_from'],
                'period_to' => $validated['period_to'],
                'date' => $validated['period_from'], // 申請日の基準
                'amount' => 0, // 子レコードの合計で計算
                'description' => '交通費請求明細書',
                'status' => Expense::STATUS_PENDING,
            ]);

            // 子レコード（明細）を作成
            $totalAmount = 0;
            foreach ($validated['items'] as $item) {
                $totalAmount += $item['amount'];
                Expense::create([
                    'user_id' => Auth::id(),
                    'parent_id' => $parentExpense->id,
                    'category' => '交通費',
                    'is_transportation' => false,
                    'date' => $item['date'],
                    'amount' => $item['amount'],
                    'description' => $item['business'],
                    'vehicle' => $item['vehicle'],
                    'route_from' => $item['route_from'],
                    'route_via' => $item['route_via'] ?? null,
                    'route_to' => $item['route_to'],
                    'transportation_type' => $item['transportation_type'],
                    'status' => Expense::STATUS_PENDING,
                ]);
            }

            // 親レコードの合計金額を更新
            $parentExpense->update(['amount' => $totalAmount]);

            // 承認フローを作成（業務部）
            $this->createExpenseApprovalFlow($parentExpense);
        });

        // トランザクション外でExcel生成とメール送信
        try {
            $this->generateExcelAndSendEmail($parentExpense);
        } catch (\Exception $e) {
            \Log::error('Excel生成・メール送信エラー: ' . $e->getMessage());
            return redirect()->route('expenses.index')
                ->with('warning', '交通費申請は登録されましたが、メール送信でエラーが発生しました。');
        }

        return redirect()->route('expenses.index')
            ->with('success', '交通費申請を送信しました。業務部に通知を送信しました。');
    }

    /**
     * Display the specified resource.
     */
    public function show(Expense $expense)
    {
        $this->authorize('view', $expense);
        
        // 交通費申請の場合は明細も読み込む
        if ($expense->isTransportation()) {
            $expense->load('children');
        }
        
        // 承認履歴も読み込む
        $expense->load('workflowApprovals.approver');
        
        return view('expenses.show', compact('expense'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expense $expense)
    {
        $this->authorize('update', $expense);
        
        return view('expenses.edit', compact('expense'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ExpenseRequest $request, Expense $expense)
    {
        $this->authorize('update', $expense);

        $validated = $request->validated();

        // 領収書の更新
        if ($request->hasFile('receipt')) {
            // 古いファイルを削除
            if ($expense->receipt_path) {
                Storage::disk('public')->delete($expense->receipt_path);
            }
            
            $file = $request->file('receipt');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $validated['receipt_path'] = $file->storeAs('receipts', $fileName, 'public');
        }

        // 差し戻し状態の場合は再申請として扱う
        $isReapplication = $expense->status === Expense::STATUS_REJECTED;
        
        $validated['status'] = Expense::STATUS_PENDING; // 再申請の場合は申請中に戻す

        $expense->update($validated);

        // 差し戻し後の再申請の場合、既存の承認フローを削除して新しく作成
        if ($isReapplication) {
            // 既存の承認フローを削除
            $expense->workflowApprovals()->delete();
            
            // 新しい承認フローを作成
            $this->createExpenseApprovalFlow($expense);
            
            // Excelを再生成してメール送信
            $this->generateExcelAndSendEmail($expense);
            
            return redirect()->route('expenses.show', $expense)
                ->with('success', '経費申請を再申請しました。業務部に通知を送信しました。');
        }

        return redirect()->route('expenses.show', $expense)
            ->with('success', '経費申請を更新しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        $this->authorize('delete', $expense);

        // 領収書ファイルを削除
        if ($expense->receipt_path) {
            Storage::disk('public')->delete($expense->receipt_path);
        }

        $expense->delete();

        return redirect()->route('expenses.index')
            ->with('success', '経費申請を削除しました。');
    }

    /**
     * Excel生成とメール送信
     */
    private function generateExcelAndSendEmail(Expense $expense): void
    {
        // Excelファイルを生成して保存
        $excelPath = $this->generateExcel($expense);
        $expense->update(['excel_path' => $excelPath]);

        // 業務部全員にメール送信（通知のみ、Excelは添付しない）
        $this->sendEmailToBusinessDivision($expense);
    }

    /**
     * Excelファイルを生成
     */
    private function generateExcel(Expense $expense): string
    {
        // 交通費申請の場合は専用フォーマット
        if ($expense->isTransportation()) {
            return $this->generateTransportationExcel($expense);
        }

        // 通常の経費申請
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // タイトル
        $sheet->setCellValue('A1', '経費申請書');
        $sheet->mergeCells('A1:D1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // 申請日
        $sheet->setCellValue('A3', '申請日');
        $sheet->setCellValue('B3', now()->format('Y年m月d日'));

        // 申請者情報
        $row = 4;
        $sheet->setCellValue('A' . $row, '申請者');
        $sheet->setCellValue('B' . $row, $expense->user->name);
        $sheet->setCellValue('C' . $row, '社員コード');
        $sheet->setCellValue('D' . $row, $expense->user->user_code);

        $row++;
        $sheet->setCellValue('A' . $row, '所属');
        $sheet->setCellValue('B' . $row, $expense->user->division ? $expense->user->division->full_name : '未所属');

        // 経費明細
        $row += 2;
        $sheet->setCellValue('A' . $row, '経費明細');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;

        // テーブルヘッダー
        $sheet->setCellValue('A' . $row, '日付');
        $sheet->setCellValue('B' . $row, '費目');
        $sheet->setCellValue('C' . $row, '内容');
        $sheet->setCellValue('D' . $row, '金額');
        $headerStyle = $sheet->getStyle('A' . $row . ':D' . $row);
        $headerStyle->getFont()->setBold(true);
        $headerStyle->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');
        $headerStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // データ行
        $row++;
        $sheet->setCellValue('A' . $row, $expense->date->format('Y/m/d'));
        $sheet->setCellValue('B' . $row, $expense->category);
        $sheet->setCellValue('C' . $row, $expense->description);
        $sheet->setCellValue('D' . $row, number_format($expense->amount) . '円');
        $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // 合計
        $row++;
        $sheet->setCellValue('C' . $row, '合計');
        $sheet->setCellValue('D' . $row, number_format($expense->amount) . '円');
        $sheet->getStyle('C' . $row . ':D' . $row)->getFont()->setBold(true);
        $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // 備考・承認欄
        $row += 2;
        $sheet->setCellValue('A' . $row, '備考');
        $row++;
        $sheet->mergeCells('A' . $row . ':D' . ($row + 2));
        $sheet->getStyle('A' . $row)->getAlignment()->setVertical(Alignment::VERTICAL_TOP);

        $row += 4;
        $sheet->setCellValue('A' . $row, '承認欄');
        $sheet->mergeCells('A' . $row . ':B' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;
        $sheet->mergeCells('A' . $row . ':D' . ($row + 3));

        // 列幅の調整
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(15);

        // ボーダー
        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A3:D' . ($row - 2))->applyFromArray($borderStyle);

        // ファイル保存
        $fileName = '経費申請書_' . $expense->user->user_code . '_' . $expense->date->format('Ymd') . '_' . $expense->id . '.xlsx';
        $filePath = storage_path('app/public/expenses/' . $fileName);
        
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        return 'expenses/' . $fileName;
    }

    /**
     * 交通費請求明細書のExcelファイルを生成
     */
    private function generateTransportationExcel(Expense $expense): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // タイトル
        $sheet->setCellValue('A1', '交通費請求明細書');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        
        // 提出日
        $sheet->setCellValue('H1', '提出');
        $sheet->setCellValue('I1', now()->format('Y年m月d日'));
        
        // 所属と氏名
        $row = 3;
        $sheet->setCellValue('A' . $row, '所属');
        $sheet->setCellValue('B' . $row, $expense->user->division ? $expense->user->division->full_name : '未所属');
        $sheet->setCellValue('F' . $row, '氏名');
        $sheet->setCellValue('G' . $row, $expense->user->name);
        $sheet->setCellValue('H' . $row, '㊞');
        
        // 表のヘッダー
        $row = 5;
        $sheet->setCellValue('A' . $row, '月/日');
        $sheet->setCellValue('B' . $row, '業務（セ/#）・行き先');
        $sheet->setCellValue('C' . $row, '乗物');
        $sheet->setCellValue('D' . $row, '発　～　（経由）　～　着');
        $sheet->setCellValue('E' . $row, '片道');
        $sheet->setCellValue('F' . $row, '金　　　　　額');
        $sheet->mergeCells('E' . $row . ':F' . ($row + 1));
        
        $row++;
        $sheet->setCellValue('E' . $row, '往復');
        
        // ヘッダースタイル
        $headerStyle = $sheet->getStyle('A' . ($row - 1) . ':F' . $row);
        $headerStyle->getFont()->setBold(true);
        $headerStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $headerStyle->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $headerStyle->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $headerStyle->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0E0E0');
        
        // 明細データ
        $expense->load('children');
        $row++;
        $startRow = $row;
        foreach ($expense->children as $item) {
            $sheet->setCellValue('A' . $row, $item->date->format('n/j'));
            $sheet->setCellValue('B' . $row, $item->description);
            $sheet->setCellValue('C' . $row, $item->vehicle);
            
            // 発～経由～着
            $route = $item->route_from;
            if ($item->route_via) {
                $route .= '　（' . $item->route_via . '）';
            }
            $route .= '　' . $item->route_to;
            $sheet->setCellValue('D' . $row, $route);
            
            // 片道/往復のチェック（往復の場合は黄色背景）
            if ($item->transportation_type === '往復') {
                $sheet->setCellValue('F' . $row, '往復');
                $sheet->getStyle('E' . $row . ':F' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF00'); // 黄色
            } else {
                $sheet->setCellValue('E' . $row, '片道');
            }
            
            // 金額（次の行に）
            $sheet->setCellValue('F' . ($row + 1), number_format($item->amount));
            $sheet->getStyle('F' . ($row + 1))->getNumberFormat()->setFormatCode('#,##0');
            
            // ボーダー
            $itemStyle = $sheet->getStyle('A' . $row . ':F' . ($row + 1));
            $itemStyle->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            
            $row += 2;
        }
        
        // 空の明細行（最大10行まで）
        for ($i = $row; $i < $startRow + 20; $i += 2) {
            $sheet->getStyle('A' . $i . ':F' . ($i + 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }
        
        // 合計行
        $row = $startRow + 20;
        $sheet->setCellValue('A' . $row, '期間');
        $sheet->setCellValue('B' . $row, $expense->period_from->format('n/j'));
        $sheet->setCellValue('C' . $row, '～');
        $sheet->setCellValue('D' . $row, $expense->period_to->format('n/j'));
        $sheet->setCellValue('E' . $row, '日間');
        $sheet->setCellValue('F' . $row, '¥' . number_format($expense->total_amount));
        $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('"¥"#,##0');
        $sheet->getStyle('A' . $row . ':F' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':F' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        // 承認欄
        $row += 3;
        $sheet->setCellValue('A' . $row, '受領印');
        $sheet->mergeCells('A' . $row . ':B' . ($row + 2));
        $sheet->setCellValue('C' . $row, '承認');
        $sheet->mergeCells('C' . $row . ':D' . $row);
        $sheet->setCellValue('E' . $row, '所属長');
        $sheet->setCellValue('F' . $row, '担当');
        $sheet->setCellValue('G' . $row, '支払');
        $sheet->mergeCells('G' . $row . ':H' . $row);
        
        $row++;
        $sheet->setCellValue('C' . $row, '');
        $sheet->mergeCells('C' . $row . ':D' . ($row + 1));
        $sheet->setCellValue('E' . $row, '');
        $sheet->setCellValue('F' . $row, '');
        $sheet->setCellValue('G' . $row, '年');
        $sheet->setCellValue('H' . $row, '月');
        
        $row++;
        $sheet->setCellValue('G' . $row, '日');
        
        // 承認欄のボーダー
        $approvalStyle = $sheet->getStyle('A' . ($row - 2) . ':H' . $row);
        $approvalStyle->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        // 会社名
        $row += 3;
        $sheet->setCellValue('D' . $row, '日本メカトロン株式会社');
        $sheet->getStyle('D' . $row)->getFont()->setSize(12);
        $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // 列幅の調整
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->getColumnDimension('F')->setWidth(12);
        $sheet->getColumnDimension('G')->setWidth(8);
        $sheet->getColumnDimension('H')->setWidth(8);
        $sheet->getColumnDimension('I')->setWidth(15);
        
        // ファイル保存
        $fileName = '交通費請求明細書_' . $expense->user->user_code . '_' . $expense->period_from->format('Ymd') . '_' . $expense->id . '.xlsx';
        $filePath = storage_path('app/public/expenses/' . $fileName);
        
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        return 'expenses/' . $fileName;
    }

    /**
     * 業務部全員にメール送信（通知のみ）
     */
    private function sendEmailToBusinessDivision(Expense $expense): void
    {
        // 「業務部」を取得（親部署）
        $businessDivision = Division::where('name', '業務部')->whereNull('parent_id')->first();

        if (!$businessDivision) {
            return;
        }

        // 業務部に所属するユーザー（親部署と子部署の両方）
        $users = User::where('delete_flg', 0)
            ->where(function ($query) use ($businessDivision) {
                // 親部署「業務部」に所属
                $query->where('division_id', $businessDivision->id)
                    // または子部署「業務課」に所属
                    ->orWhereIn('division_id', function ($subQuery) use ($businessDivision) {
                        $subQuery->select('id')
                            ->from('divisions')
                            ->where('parent_id', $businessDivision->id);
                    });
            })
            ->get();

        if ($users->isEmpty()) {
            return;
        }

        // 全員にメール送信（Excelは添付しない）
        foreach ($users as $user) {
            if ($user->email) {
                Mail::to($user->email)->send(new ExpenseNotification($expense, $expense->excel_path ?? ''));
            }
        }
    }

    /**
     * Excelファイルをダウンロード
     */
    public function downloadExcel(Expense $expense)
    {
        $this->authorize('view', $expense);

        // Excelファイルが存在しない場合は再生成
        if (!$expense->excel_path || !Storage::disk('public')->exists($expense->excel_path)) {
            $expense->excel_path = $this->generateExcel($expense);
            $expense->save();
        }

        $filePath = storage_path('app/public/' . $expense->excel_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'ファイルが見つかりません。');
        }

        $fileName = $expense->isTransportation() 
            ? '交通費請求明細書_' . $expense->user->user_code . '_' . $expense->period_from->format('Ymd') . '.xlsx'
            : '経費申請書_' . $expense->user->user_code . '_' . $expense->date->format('Ymd') . '.xlsx';

        return response()->download($filePath, $fileName);
    }

    /**
     * 経費申請の承認フローを作成（業務部）
     */
    private function createExpenseApprovalFlow(Expense $expense): void
    {
        $businessDivision = Division::where('name', '業務部')->whereNull('parent_id')->first();
        if (!$businessDivision) {
            return;
        }

        // 業務部のユーザー全員に承認権限を付与
        $businessUsers = User::where('delete_flg', 0)
            ->where(function ($query) use ($businessDivision) {
                $query->where('division_id', $businessDivision->id)
                    ->orWhereIn('division_id', $businessDivision->children->pluck('id'));
            })
            ->get();

        foreach ($businessUsers as $businessUser) {
            WorkflowApproval::create([
                'request_type' => 'expense',
                'request_id' => $expense->id,
                'applicant_id' => $expense->user_id,
                'approval_order' => 1,
                'approver_id' => $businessUser->id,
                'is_final_approval' => 1,
                'status' => WorkflowApproval::STATUS_PENDING,
            ]);
        }
    }
}
