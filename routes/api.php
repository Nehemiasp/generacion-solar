<?php

use App\Http\Controllers\Api\V1\AlertaController;
use App\Http\Controllers\Api\V1\DepartamentoController;
use App\Http\Controllers\Api\V1\EstadisticaController;
use App\Http\Controllers\Api\V1\GeneracionController;
use App\Http\Controllers\Api\V1\GranjaController;
use Illuminate\Support\Facades\Route;

// API pública de solo lectura, versionada (RF-16). Rate limit: 60 req/min por IP.
Route::prefix('v1')->middleware('throttle:api')->group(function () {
    Route::get('departamentos', [DepartamentoController::class, 'index']);
    Route::get('departamentos/{departamento}', [DepartamentoController::class, 'show']);

    Route::get('granjas/mapa', [GranjaController::class, 'mapa']);
    Route::get('granjas', [GranjaController::class, 'index']);
    Route::get('granjas/{granja}', [GranjaController::class, 'show']);
    Route::get('granjas/{granja}/generaciones', [GranjaController::class, 'generaciones']);
    Route::get('granjas/{granja}/proyeccion', [GranjaController::class, 'proyeccion']);

    Route::get('generaciones', [GeneracionController::class, 'index']);

    Route::get('estadisticas/nacional', [EstadisticaController::class, 'nacional']);
    Route::get('estadisticas/departamentos', [EstadisticaController::class, 'departamentos']);

    Route::get('alertas', [AlertaController::class, 'index']);
});
