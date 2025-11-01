<?php

namespace App\Http\Controllers;

use App\Http\Requests\TravelRequest as TravelRequestFormRequest;
use App\Models\TravelRequest;
use App\Models\TravelExpense;
use App\Models\Division;
use App\Models\User;
use App\Models\WorkflowApproval;
use App\Mail\TravelNotification;
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

class TravelRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TravelRequest::with('user')
            ->where('user_id', Auth::id())
            ->latest('departure_date');

        // ステータスでフィルタリング
        if ($request->has('status')) {
            match($request->status) {
                'pending' => $query->pending(),
                'approved' => $query->approved(),
                default => null,
            };
        }

        $travelRequests = $query->paginate(15);

        return view('travel-requests.index', compact('travelRequests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('travel-requests.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TravelRequestFormRequest $request)
    {
        $validated = $request->validated();

        // トランザクション処理
        DB::transaction(function () use ($validated, &$travelRequest) {
            // 出張申請を作成
            $travelRequest = TravelRequest::create([
                'user_id' => Auth::id(),
                'destination' => $validated['destination'],
                'purpose' => $validated['purpose'],
                'departure_date' => $validated['departure_date'],
                'return_date' => $validated['return_date'],
                'advance_payment' => $validated['advance_payment'] ?? 0,
                'status' => TravelRequest::STATUS_PENDING,
            ]);

            // 経費明細を作成
            foreach ($validated['expenses'] as $expenseData) {
                TravelExpense::create([
                    'travel_request_id' => $travelRequest->id,
                    'date' => $expenseData['date'],
                    'description' => $expenseData['description'],
                    'category' => $expenseData['category'],
                    'cash' => $expenseData['cash'] ?? 0,
                    'ticket' => $expenseData['ticket'] ?? 0,
                    'remarks' => $expenseData['remarks'] ?? null,
                ]);
            }

            // 小計と精算金額を計算
            $travelRequest->calculateSubtotal();

            // 承認フローを作成（部署責任者 → 業務部）
            $this->createTravelApprovalFlow($travelRequest);
        });

        // トランザクション外でExcel生成とメール送信
        try {
            $this->generateExcelAndSendEmail($travelRequest);
        } catch (\Exception $e) {
            \Log::error('Excel生成・メール送信エラー: ' . $e->getMessage());
            return redirect()->route('travel-requests.index')
                ->with('warning', '出張申請は登録されましたが、メール送信でエラーが発生しました。');
        }

        return redirect()->route('travel-requests.index')
            ->with('success', '出張申請を送信しました。業務部に通知を送信しました。');
    }

    /**
     * Display the specified resource.
     */
    public function show(TravelRequest $travelRequest)
    {
        $this->authorize('view', $travelRequest);
        
        $travelRequest->load(['user', 'travelExpenses', 'workflowApprovals.approver']);
        
        return view('travel-requests.show', compact('travelRequest'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TravelRequest $travelRequest)
    {
        $this->authorize('update', $travelRequest);
        
        $travelRequest->load('travelExpenses');
        
        return view('travel-requests.edit', compact('travelRequest'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TravelRequest $travelRequest)
    {
        $this->authorize('update', $travelRequest);

        $validated = $request->validate([
            'destination' => 'required|string|max:255',
            'purpose' => 'required|string|max:500',
            'departure_date' => 'required|date',
            'return_date' => 'required|date|after:departure_date',
            'advance_payment' => 'nullable|numeric|min:0',
            'expenses' => 'required|array|min:1',
            'expenses.*.date' => 'required|date',
            'expenses.*.description' => 'required|string|max:255',
            'expenses.*.category' => 'required|string|in:交通費,宿泊費,日当,半日当,その他',
            'expenses.*.cash' => 'nullable|numeric|min:0',
            'expenses.*.ticket' => 'nullable|numeric|min:0',
            'expenses.*.remarks' => 'nullable|string|max:500',
        ]);

        // 出張申請を更新
        $travelRequest->update([
            'destination' => $validated['destination'],
            'purpose' => $validated['purpose'],
            'departure_date' => $validated['departure_date'],
            'return_date' => $validated['return_date'],
            'advance_payment' => $validated['advance_payment'] ?? 0,
        ]);

        // 既存の経費明細を削除
        $travelRequest->travelExpenses()->delete();

        // 新しい経費明細を作成
        foreach ($validated['expenses'] as $expenseData) {
            TravelExpense::create([
                'travel_request_id' => $travelRequest->id,
                'date' => $expenseData['date'],
                'description' => $expenseData['description'],
                'category' => $expenseData['category'],
                'cash' => $expenseData['cash'] ?? 0,
                'ticket' => $expenseData['ticket'] ?? 0,
                'remarks' => $expenseData['remarks'] ?? null,
            ]);
        }

        // 小計と精算金額を再計算
        $travelRequest->calculateSubtotal();

        return redirect()->route('travel-requests.index')
            ->with('success', '出張申請を更新しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TravelRequest $travelRequest)
    {
        $this->authorize('delete', $travelRequest);

        $travelRequest->delete();

        return redirect()->route('travel-requests.index')
            ->with('success', '出張申請を削除しました。');
    }

    /**
     * Excel生成とメール送信
     */
    private function generateExcelAndSendEmail(TravelRequest $travelRequest): void
    {
        $excelPath = $this->generateExcel($travelRequest);
        $this->sendEmailToBusinessDivision($travelRequest, $excelPath);
    }

    /**
     * Excelファイルを生成
     */
    private function generateExcel(TravelRequest $travelRequest): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // タイトル
        $sheet->setCellValue('A1', '出張申請書');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // 申請日
        $sheet->setCellValue('A3', '申請日');
        $sheet->setCellValue('B3', now()->format('Y年m月d日'));

        // 申請者情報
        $sheet->setCellValue('A4', '申請者');
        $sheet->setCellValue('B4', $travelRequest->user->name . ' (' . $travelRequest->user->user_code . ')');
        $sheet->setCellValue('A5', '所属部署');
        $sheet->setCellValue('B5', $travelRequest->user->division->full_name ?? '未設定');

        // 出張情報
        $sheet->setCellValue('A7', '出張先');
        $sheet->setCellValue('B7', $travelRequest->destination);
        $sheet->setCellValue('A8', '目的');
        $sheet->setCellValue('B8', $travelRequest->purpose);
        $sheet->setCellValue('A9', '出発日');
        $sheet->setCellValue('B9', $travelRequest->departure_date->format('Y年m月d日'));
        $sheet->setCellValue('A10', '帰着日');
        $sheet->setCellValue('B10', $travelRequest->return_date->format('Y年m月d日'));
        $sheet->setCellValue('A11', '前払金');
        $sheet->setCellValue('B11', number_format($travelRequest->advance_payment, 0));
        $sheet->getStyle('B11')->getNumberFormat()->setFormatCode('#,##0');

        // 経費明細ヘッダー
        $sheet->setCellValue('A13', '日付');
        $sheet->setCellValue('B13', '費目');
        $sheet->setCellValue('C13', '内容');
        $sheet->setCellValue('D13', '現金');
        $sheet->setCellValue('E13', 'チケット');
        $sheet->setCellValue('F13', '合計');
        $sheet->getStyle('A13:F13')->getFont()->setBold(true);
        $sheet->getStyle('A13:F13')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFEEEEEE');
        $sheet->getStyle('A13:F13')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // 経費明細
        $row = 14;
        $travelRequest->load('travelExpenses');
        foreach ($travelRequest->travelExpenses as $expense) {
            $sheet->setCellValue('A' . $row, $expense->date->format('Y/m/d'));
            $sheet->setCellValue('B' . $row, $expense->category);
            $sheet->setCellValue('C' . $row, $expense->description);
            $sheet->setCellValue('D' . $row, $expense->cash);
            $sheet->setCellValue('E' . $row, $expense->ticket);
            $sheet->setCellValue('F' . $row, $expense->total);
            
            $sheet->getStyle('D' . $row . ':F' . $row)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('A' . $row . ':F' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            
            if ($expense->remarks) {
                $sheet->setCellValue('G' . $row, '備考: ' . $expense->remarks);
            }
            
            $row++;
        }

        // 小計・精算金額
        $row++;
        $sheet->setCellValue('C' . $row, '小計');
        $sheet->setCellValue('F' . $row, $travelRequest->subtotal);
        $sheet->getStyle('C' . $row . ':F' . $row)->getFont()->setBold(true);
        $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('C' . $row . ':F' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFDDDDDD');
        $sheet->getStyle('C' . $row . ':F' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $row++;
        $sheet->setCellValue('C' . $row, '前払金');
        $sheet->setCellValue('F' . $row, $travelRequest->advance_payment);
        $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('C' . $row . ':F' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $row++;
        $sheet->setCellValue('C' . $row, '精算金額（' . $travelRequest->settlement_type_label . '）');
        $sheet->setCellValue('F' . $row, abs($travelRequest->settlement_amount));
        $sheet->getStyle('C' . $row . ':F' . $row)->getFont()->setBold(true);
        $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('C' . $row . ':F' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFDDDD');
        $sheet->getStyle('C' . $row . ':F' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // カラム幅の自動調整
        foreach (range('A', 'G') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $fileName = '出張申請書_' . $travelRequest->user->user_code . '_' . $travelRequest->departure_date->format('Ymd') . '_' . $travelRequest->id . '.xlsx';
        $filePath = 'travel-requests/' . $fileName;
        $fullPath = Storage::disk('public')->path($filePath);

        // ディレクトリが存在しない場合は作成
        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($fullPath);

        return $filePath;
    }

    /**
     * 業務部にメール送信（通知のみ）
     */
    private function sendEmailToBusinessDivision(TravelRequest $travelRequest): void
    {
        $businessDivision = Division::where('name', '業務部')->whereNull('parent_id')->first();
        if ($businessDivision) {
            $recipients = $businessDivision->users->pluck('email')->toArray();
            
            // 業務課のユーザーも追加
            foreach ($businessDivision->children as $childDivision) {
                if ($childDivision->name === '業務課') {
                    $recipients = array_merge($recipients, $childDivision->users->pluck('email')->toArray());
                }
            }
            $recipients = array_unique($recipients);

            if (!empty($recipients)) {
                Mail::to($recipients)->send(new TravelNotification($travelRequest, $travelRequest->excel_path ?? ''));
            }
        }
    }

    /**
     * Excelファイルをダウンロード
     */
    public function downloadExcel(TravelRequest $travelRequest)
    {
        $this->authorize('view', $travelRequest);

        // Excelファイルが存在しない場合は再生成
        if (!$travelRequest->excel_path || !Storage::disk('public')->exists($travelRequest->excel_path)) {
            $travelRequest->excel_path = $this->generateExcel($travelRequest);
            $travelRequest->save();
        }

        $filePath = storage_path('app/public/' . $travelRequest->excel_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'ファイルが見つかりません。');
        }

        $fileName = '出張申請書_' . $travelRequest->user->user_code . '_' . $travelRequest->departure_date->format('Ymd') . '.xlsx';

        return response()->download($filePath, $fileName);
    }

    /**
     * 出張申請の承認フローを作成（部署責任者 → 業務部）
     */
    private function createTravelApprovalFlow(TravelRequest $travelRequest): void
    {
        $user = $travelRequest->user;
        
        // 第1ステップ：部署責任者
        if ($user->division && $user->division->manager_id) {
            WorkflowApproval::create([
                'request_type' => 'travel',
                'request_id' => $travelRequest->id,
                'applicant_id' => $travelRequest->user_id,
                'approval_order' => 1,
                'approver_id' => $user->division->manager_id,
                'is_final_approval' => 0,
                'status' => WorkflowApproval::STATUS_PENDING,
            ]);
        }

        // 第2ステップ：業務部（最終承認）- 部署責任者承認後に作成されるため、ここでは作成しない
        // ApprovalControllerのcreateNextApprovalStep()で作成される
    }
}

