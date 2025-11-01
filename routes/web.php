<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // お知らせ
    Route::resource('news', \App\Http\Controllers\NewsController::class);

    // 各種資料（ドキュメント）
    Route::resource('documents', \App\Http\Controllers\DocumentController::class);
    Route::get('documents/{document}/download', [\App\Http\Controllers\DocumentController::class, 'download'])
        ->name('documents.download');

    // FAQ
    Route::resource('faqs', \App\Http\Controllers\FAQController::class);
    Route::post('faqs/{faq}/helpful', [\App\Http\Controllers\FAQController::class, 'helpful'])
        ->name('faqs.helpful');

    // ご意見箱（問い合わせ）
    Route::resource('inquiries', \App\Http\Controllers\InquiryController::class);
    Route::post('inquiries/{inquiry}/reply', [\App\Http\Controllers\InquiryController::class, 'reply'])
        ->name('inquiries.reply');
    Route::patch('inquiries/{inquiry}/status', [\App\Http\Controllers\InquiryController::class, 'updateStatus'])
        ->name('inquiries.updateStatus');

    // 会社の色々
    Route::get('company-info', [\App\Http\Controllers\CompanyInfoController::class, 'index'])->name('company-info.index');
    
    // 社員紹介
    Route::get('users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');

    // 経費精算
    Route::get('expenses/menu', [\App\Http\Controllers\ExpenseMenuController::class, 'index'])->name('expenses.menu');
    Route::resource('expenses', \App\Http\Controllers\ExpenseController::class);
    Route::get('expenses/{expense}/download-excel', [\App\Http\Controllers\ExpenseController::class, 'downloadExcel'])->name('expenses.download-excel');

    // 出張申請
    Route::resource('travel-requests', \App\Http\Controllers\TravelRequestController::class);
    Route::get('travel-requests/{travelRequest}/download-excel', [\App\Http\Controllers\TravelRequestController::class, 'downloadExcel'])->name('travel-requests.download-excel');

    // 承認
    Route::get('approvals', [\App\Http\Controllers\ApprovalController::class, 'index'])->name('approvals.index');
    Route::get('api/pending-approvals-count', [\App\Http\Controllers\ApprovalController::class, 'getPendingCount'])->name('api.pending-approvals-count');
    Route::post('approvals/{approval}/approve', [\App\Http\Controllers\ApprovalController::class, 'approve'])->name('approvals.approve');
    Route::post('approvals/{approval}/reject', [\App\Http\Controllers\ApprovalController::class, 'reject'])->name('approvals.reject');

    // マスタ管理（管理者のみ）
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('masters', [\App\Http\Controllers\Admin\MasterController::class, 'index'])->name('masters.index');
        Route::resource('users', \App\Http\Controllers\Admin\UserManagementController::class);
        Route::resource('divisions', \App\Http\Controllers\Admin\DivisionManagementController::class);
    });
});

require __DIR__.'/auth.php';
