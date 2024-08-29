<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    logs()->info('response');
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
