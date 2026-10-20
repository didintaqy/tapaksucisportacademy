<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentCallbackController;
use App\Http\Controllers\PaymentPortalController;
use App\Http\Controllers\PaymentReceiptController;
use App\Http\Controllers\ProgramController;
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

// Kwitansi pembayaran (khusus admin / operator yang sudah login)
Route::get('/kwitansi/{payment}', [PaymentReceiptController::class, 'show'])
    ->middleware('auth')
    ->name('payments.receipt');

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
