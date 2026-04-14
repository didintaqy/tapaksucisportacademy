<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\HomeController;

Route::get('/', HomeController::class)->name('home');
Route::get('/about', AboutController::class)->name('about');