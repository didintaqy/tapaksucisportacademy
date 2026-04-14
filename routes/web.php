<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\HomeConteroller;

Route::get('/', HomeConteroller::class)->name('home');
Route::get('/about', AboutController::class)->name('about');
