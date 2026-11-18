<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\AchievementController;
use App\Http\Controllers\Admin\FinancialReportController;
use App\Http\Controllers\BeltPromotionCertificateController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImageDeliveryController;
use App\Http\Controllers\PaymentCallbackController;
use App\Http\Controllers\PaymentPortalController;
use App\Http\Controllers\PaymentReceiptController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\StorageFallbackController;
use App\Http\Controllers\StudentPortalController;
use App\Http\Controllers\StudentRegistrationController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\TrainingScheduleController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/health', HealthController::class)->name('health');
Route::get('/about', AboutController::class)->name('about');
Route::get('/program', [ProgramController::class, 'index'])->name('program');
Route::get('/program/{program:slug}', [ProgramController::class, 'show'])->name('program.show');
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

// Kalender Kegiatan & Prestasi Atlet
Route::get('/kalender', [CalendarController::class, '__invoke'])->name('calendar');
Route::get('/prestasi', AchievementController::class)->name('achievements');

Route::get('/pendaftaran', [StudentRegistrationController::class, 'index'])->name('registration');
Route::post('/pendaftaran', [StudentRegistrationController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('registration.store');

// Sitemap (SEO)
Route::get('/sitemap.xml', [SitemapController::class, '__invoke'])->name('sitemap');

// robots.txt
Route::get('/robots.txt', RobotsController::class)->name('robots');

// On-demand optimized image variants (webp/avif)
Route::get('/images/{model}/{id}/{variant}', ImageDeliveryController::class)
    ->where('variant', 'webp|avif')
    ->name('images.variant');

// Fallback penyajian berkas publik bila symlink storage belum tersedia.
// Bila `public/storage` ada, web server menyajikan berkas lebih dulu.
Route::get('/storage/{path}', StorageFallbackController::class)
    ->where('path', '.*')
    ->name('storage.fallback');

// Kwitansi pembayaran (khusus admin / operator yang sudah login)
Route::get('/kwitansi/{payment}', [PaymentReceiptController::class, 'show'])
    ->middleware('auth')
    ->name('payments.receipt');

// Sertifikat UKT (admin, operator, atau pelatih yang berhak)
Route::get('/sertifikat-ukt/{beltPromotion}', [BeltPromotionCertificateController::class, 'show'])
    ->middleware('auth')
    ->name('belt-promotions.certificate');

// Export laporan keuangan (admin / operator)
Route::middleware('auth')->prefix('admin/reports')->name('admin.reports.')->group(function (): void {
    Route::get('/income/export', [FinancialReportController::class, 'exportIncome'])->name('income.export');
    Route::get('/income/excel', [FinancialReportController::class, 'exportIncomeExcel'])->name('income.excel');
    Route::get('/income/pdf', [FinancialReportController::class, 'exportIncomePdf'])->name('income.pdf');
    Route::get('/outstanding/export', [FinancialReportController::class, 'exportOutstanding'])->name('outstanding.export');
    Route::get('/outstanding/excel', [FinancialReportController::class, 'exportOutstandingExcel'])->name('outstanding.excel');
    Route::get('/outstanding/pdf', [FinancialReportController::class, 'exportOutstandingPdf'])->name('outstanding.pdf');
    Route::post('/queue-export', [FinancialReportController::class, 'queueExport'])->name('queue-export');
});

// Portal Siswa & Analisis Kebugaran (REMAJA)
Route::get('/portal-siswa', [StudentPortalController::class, 'index'])
    ->name('student.portal');
Route::post('/portal-siswa/masuk', [StudentPortalController::class, 'login'])
    ->middleware('throttle:10,1')
    ->name('student.portal.login');
Route::get('/portal-siswa/dashboard', [StudentPortalController::class, 'dashboard'])
    ->name('student.portal.dashboard');
Route::get('/portal-siswa/rapor-pdf', [StudentPortalController::class, 'downloadReportPdf'])
    ->name('student.portal.report-pdf');
Route::get('/portal-siswa/sertifikat-ukt/{beltPromotion}', [StudentPortalController::class, 'certificate'])
    ->name('student.portal.certificate');
Route::get('/portal-siswa/keluar', [StudentPortalController::class, 'logout'])
    ->name('student.portal.logout');

// Portal pembayaran untuk orang tua / siswa
Route::get('/pembayaran', [PaymentPortalController::class, 'index'])
    ->name('payments.portal');
Route::post('/pembayaran/cari', [PaymentPortalController::class, 'search'])
    ->middleware('throttle:10,1')
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
    ->middleware('throttle:120,1')
    ->name('payments.callback');
