<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
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

    // 出張申請
    Route::resource('travel-requests', \App\Http\Controllers\TravelRequestController::class);
});

require __DIR__.'/auth.php';
