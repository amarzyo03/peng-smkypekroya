<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Siswa;
use App\Livewire\Uploadsk;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::get('/siswa', Siswa::class)->name('siswa');
    Route::get('/upload-sk', Uploadsk::class)->name('upload-sk');
});

require __DIR__ . '/settings.php';
