<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\InstitusiController;
use App\Http\Controllers\InstrumenController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ObservasiController;
use App\Http\Controllers\PinController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/institusi', [InstitusiController::class, 'edit'])->name('institusi');
Route::put('/institusi', [InstitusiController::class, 'update'])->name('institusi.update');

Route::get('/database-guru', [GuruController::class, 'index'])->name('guru');
Route::post('/database-guru', [GuruController::class, 'store'])->name('guru.store');
Route::put('/database-guru/{guru}', [GuruController::class, 'update'])->name('guru.update');
Route::delete('/database-guru/{guru}', [GuruController::class, 'destroy'])->name('guru.destroy');

Route::get('/instrumen', [InstrumenController::class, 'index'])->name('instrumen');
Route::put('/instrumen/sync', [InstrumenController::class, 'sync'])->name('instrumen.sync');
Route::post('/instrumen', [InstrumenController::class, 'store'])->name('instrumen.store');
Route::put('/instrumen/{instrument}', [InstrumenController::class, 'update'])->name('instrumen.update');
Route::delete('/instrumen/{instrument}', [InstrumenController::class, 'destroy'])->name('instrumen.destroy');
Route::post('/instrumen/{instrument}/move', [InstrumenController::class, 'moveInstrument'])->name('instrumen.move');
Route::post('/instrumen/{instrument}/aspek', [InstrumenController::class, 'storeAspek'])->name('instrumen.aspek.store');
Route::delete('/instrumen/aspek/{instrument_aspek}', [InstrumenController::class, 'destroyAspek'])->name('instrumen.aspek.destroy');
Route::post('/instrumen/aspek/{instrument_aspek}/move', [InstrumenController::class, 'moveAspek'])->name('instrumen.aspek.move');
Route::post('/instrumen/aspek/{instrument_aspek}/indikator', [InstrumenController::class, 'storeIndikator'])->name('instrumen.indikator.store');
Route::delete('/instrumen/indikator/{instrument_indikator}', [InstrumenController::class, 'destroyIndikator'])->name('instrumen.indikator.destroy');
Route::post('/instrumen/indikator/{instrument_indikator}/move', [InstrumenController::class, 'moveIndikator'])->name('instrumen.indikator.move');

Route::get('/observasi', [ObservasiController::class, 'index'])->name('observasi');
Route::post('/observasi', [ObservasiController::class, 'store'])->name('observasi.store');

Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');
Route::post('/laporan/analisis', [LaporanController::class, 'store'])->name('laporan.store');
Route::get('/laporan/preview/{guru_id}', [LaporanController::class, 'preview'])->name('laporan.preview');
Route::get('/laporan/export/word/{guru_id}', [LaporanController::class, 'exportWord'])->name('laporan.export.word');
Route::get('/laporan/export/pdf/{guru_id}', [LaporanController::class, 'exportPdf'])->name('laporan.export.pdf');

Route::post('/reset-sesi', function () {
    session()->flush();

    return redirect()->route('dashboard')->with('status', 'Sesi telah direset.');
})->name('session.reset');

Route::get('/verify-pin', [PinController::class, 'show'])->name('pin.verify');
Route::post('/verify-pin', [PinController::class, 'verify'])->name('pin.verify.post');
