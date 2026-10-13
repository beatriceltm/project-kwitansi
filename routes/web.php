<?php

use App\Http\Controllers\KuitansiController;
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

        Route::view('dashboard', 'dashboard')
            ->name('dashboard');

        Route::livewire(
            'transaksi/create',
            'pages::transaksi.create'
        )->name('transaksi.create');

        Route::livewire(
            'transaksi',
            'pages::transaksi.index'
        )->name('transaksi.index');

        Route::get(
            'transaksi/{transaksi}/kuitansi',
            [KuitansiController::class, 'show']
        )->name('transaksi.kuitansi');

    });

require __DIR__.'/settings.php';
