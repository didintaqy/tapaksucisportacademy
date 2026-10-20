<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\PaymentCallbackController;
use App\Http\Controllers\PaymentPortalController;
use App\Http\Controllers\PaymentReceiptController;

Route::get('/', HomeController::class)->name('home');
Route::get('/about', AboutController::class)->name('about');
Route::get('/program', ProgramController::class)->name('program');
Route::get('/contact-us', ContactUsController::class)->name('contact-us');
Route::post('/contact-us', [ContactUsController::class, 'store'])->name('contact-us.store');
Route::get('/gallery', GalleryController::class)->name('gallery');
Route::get('/blog', [BlogController::class, 'index'])->name('blog');
Route::get('/blog/{post:slug}', [BlogController::class, 'show'])->name('blog.show');

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
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
    ->name('payments.callback');
