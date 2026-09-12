<?php

use App\Http\Controllers\PublicoController;
use Illuminate\Support\Facades\Route;

// Frontend público (sin login): tablero, mapa, reporte, ficha de granja.
Route::get('/', [PublicoController::class, 'inicio'])->name('inicio');
Route::get('/mapa', [PublicoController::class, 'mapa'])->name('mapa');
Route::get('/reporte', [PublicoController::class, 'reporte'])->name('reporte');
Route::get('/alertas', [PublicoController::class, 'alertas'])->name('alertas');
Route::get('/granjas/{granja}', [PublicoController::class, 'granja'])->name('granjas.show');
