<?php

use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::prefix('{current_team}')
    ->middleware([
        'auth',
        'verified',
        EnsureTeamMembership::class
    ])
    ->group(function () {

        // Dashboard
        Route::view('dashboard', 'dashboard')
            ->name('dashboard');

        // Halaman tambah transaksi
        Route::livewire(
            'transaksi/create',
            'pages::transaksi.create'
        )->name('transaksi.create');
        
Route::livewire(
    'transaksi',
    'pages::transaksi.index'
)->name('transaksi.index');

    });

require __DIR__.'/settings.php';
