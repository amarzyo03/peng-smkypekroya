<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Siswa;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::get('/siswa', Siswa::class)->name('siswa');
});

require __DIR__ . '/settings.php';
