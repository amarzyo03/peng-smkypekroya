<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Siswa;
use App\Livewire\Uploadsk;
use App\Livewire\Importsiswa;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::get('/siswa', Siswa::class)->name('siswa');
    Route::get('/upload-sk', Uploadsk::class)->name('upload-sk');
    Route::get('/import-siswa', importSiswa::class)->name('import-siswa');
});

require __DIR__ . '/settings.php';
