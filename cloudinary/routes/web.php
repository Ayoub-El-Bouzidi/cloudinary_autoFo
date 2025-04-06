<?php

use App\Http\Controllers\ImageController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/upload', [ImageController::class, 'showForm'])->name('upload.form');
Route::post('/upload', [ImageController::class, 'upload'])->name('upload.image');
