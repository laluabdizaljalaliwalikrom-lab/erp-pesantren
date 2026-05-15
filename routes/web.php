<?php

use App\Http\Controllers\ReceiptController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Models\Bill;
use App\Services\MidtransService;

Route::get('/test-payment/{bill_id}', function ($bill_id, MidtransService $service) {
    $bill = Bill::findOrFail($bill_id);
    $snapToken = $service->createSnapToken($bill);

    return view('payment_page', ['snapToken' => $snapToken, 'bill' => $bill]);
});

use App\Models\Announcement;
use App\Models\Student;
use App\Enums\StudentStatus;

Route::get('/', function () {
    $stats = [
        'students_count' => Student::where('status', StudentStatus::ACTIVE)->count(),
        'alumni_count' => Student::where('status', StudentStatus::GRADUATED)->count(),
        'institutions_count' => \App\Models\Institution::count(),
        'classes_count' => \App\Models\SchoolClass::count(),
    ];

    $news = \App\Models\Post::where('is_published', true)
        ->latest('published_at')
        ->take(3)
        ->get();

    return view('landing', compact('stats', 'news'));
});

// Payment Receipt — legacy single-layout
Route::get('/receipt/{payment}/download', [ReceiptController::class, 'print'])
    ->name('receipt.download')
    ->where('payment', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');

// Payment Receipt — dual layout (a4 | thermal)
Route::get('/payments/{payment}/print/{layout?}', [ReceiptController::class, 'print'])
    ->name('receipt.print')
    ->where('payment', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}')
    ->where('layout', 'a4|thermal');

// Public shared link — defaults to A4
Route::get('/receipt/share/{payment}', [ReceiptController::class, 'print'])
    ->name('receipt.share')
    ->where('payment', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');
Route::get('/receipt/batch/{transactionId}', [ReceiptController::class, 'printBatch'])
    ->name('receipt.batch.print');

Route::get('/students/{student}/statement', function (\App\Models\Student $student) {
    $bills = $student->bills()
        ->whereIn('status', [\App\Enums\BillStatus::UNPAID, \App\Enums\BillStatus::PARTIAL])
        ->with('fee')
        ->orderBy('due_date')
        ->get();

    return view('filament.pages.parts.print-statement', [
        'student' => $student,
        'bills' => $bills,
        'total' => $bills->sum('remaining_balance'),
    ]);
})->name('student.statement');

Route::get('/migrate-db', function () {
    try {
        // 'migrate:fresh' akan menghapus SEMUA tabel dan mengulang dari awal secara bersih
        Artisan::call('migrate:fresh', [
            '--force' => true,
            '--seed' => true,
        ]);
        return "Gagah! Database berhasil di-reset dan dimigrasi dari nol: <br><pre>" . Artisan::output() . "</pre>";
    } catch (\Exception $e) {
        return "Migration failed: " . $e->getMessage();
    }
});
