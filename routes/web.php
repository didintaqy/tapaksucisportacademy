<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\Admin\FinancialReportController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentCallbackController;
use App\Http\Controllers\PaymentPortalController;
use App\Http\Controllers\PaymentReceiptController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\StudentRegistrationController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\TrainingScheduleController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/about', AboutController::class)->name('about');
Route::get('/program', ProgramController::class)->name('program');
Route::get('/contact-us', ContactUsController::class)->name('contact-us');
Route::post('/contact-us', [ContactUsController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contact-us.store');
Route::get('/gallery', GalleryController::class)->name('gallery');
Route::get('/blog', [BlogController::class, 'index'])->name('blog');
Route::get('/blog/{post:slug}', [BlogController::class, 'show'])->name('blog.show');

Route::get('/testimoni', [TestimonialController::class, 'index'])->name('testimonials');
Route::post('/testimoni', [TestimonialController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('testimonials.store');

Route::get('/faq', FaqController::class)->name('faqs');
Route::get('/jadwal', TrainingScheduleController::class)->name('schedules');

// Kalender Kegiatan
Route::get('/kalender', [CalendarController::class, '__invoke'])->name('calendar');

Route::get('/pendaftaran', [StudentRegistrationController::class, 'index'])->name('registration');
Route::post('/pendaftaran', [StudentRegistrationController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('registration.store');

// Sitemap (SEO)
Route::get('/sitemap.xml', [SitemapController::class, '__invoke'])->name('sitemap');

// Kwitansi pembayaran (khusus admin / operator yang sudah login)
Route::get('/kwitansi/{payment}', [PaymentReceiptController::class, 'show'])
    ->middleware('auth')
    ->name('payments.receipt');

// Export laporan keuangan (admin / operator)
Route::middleware('auth')->prefix('admin/reports')->name('admin.reports.')->group(function (): void {
    Route::get('/income/export', [FinancialReportController::class, 'exportIncome'])->name('income.export');
    Route::get('/income/excel', [FinancialReportController::class, 'exportIncomeExcel'])->name('income.excel');
    Route::get('/income/pdf', [FinancialReportController::class, 'exportIncomePdf'])->name('income.pdf');
    Route::get('/outstanding/export', [FinancialReportController::class, 'exportOutstanding'])->name('outstanding.export');
    Route::get('/outstanding/excel', [FinancialReportController::class, 'exportOutstandingExcel'])->name('outstanding.excel');
    Route::get('/outstanding/pdf', [FinancialReportController::class, 'exportOutstandingPdf'])->name('outstanding.pdf');
});

// Portal pembayaran untuk orang tua / siswa
Route::get('/pembayaran', [PaymentPortalController::class, 'index'])
    ->name('payments.portal');
Route::post('/pembayaran/cari', [PaymentPortalController::class, 'search'])
    ->name('payments.portal.search');
Route::get('/pembayaran/tagihan', [PaymentPortalController::class, 'bills'])
    ->name('payments.portal.bills');
Route::post('/pembayaran/bayar/{payment}', [PaymentPortalController::class, 'checkout'])
    ->name('payments.portal.checkout');
Route::get('/pembayaran/checkout/{payment}', [PaymentPortalController::class, 'checkoutShow'])
    ->name('payments.portal.checkout.show');
Route::get('/pembayaran/kwitansi/{payment}', [PaymentPortalController::class, 'receipt'])
    ->name('payments.portal.receipt');
Route::get('/pembayaran/keluar', [PaymentPortalController::class, 'logout'])
    ->name('payments.portal.logout');

// Callback / webhook dari payment gateway (tanpa CSRF)
Route::post('/pembayaran/callback', [PaymentCallbackController::class, 'handle'])
    ->withoutMiddleware([VerifyCsrfToken::class])
    ->name('payments.callback');
