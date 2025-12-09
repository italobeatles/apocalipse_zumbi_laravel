<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SobreviventesC;
use App\Http\Controllers\Api\InventarioSobreviventesC;
use App\Http\Controllers\Api\InformarZumbificacaoC;
use App\Http\Controllers\Api\RelatorioGeralC;

Route::apiResource('sobreviventes', SobreviventesC::class); // /api/sobreviventes...

Route::get('inventario', [InventarioSobreviventesC::class, 'index']);
Route::get('inventario/{id}', [InventarioSobreviventesC::class, 'show']);
Route::post('inventario/troca', [InventarioSobreviventesC::class, 'store']);

Route::post('informar-zumbificacao', [InformarZumbificacaoC::class, 'store']);

Route::get('relatorio-geral', [RelatorioGeralC::class, 'index']);
