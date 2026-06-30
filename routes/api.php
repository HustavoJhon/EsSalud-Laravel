<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Api\DocumentController as ApiDocumentController;
use App\Http\Controllers\Api\ProcedureController as ApiProcedureController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ProcedureController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/auth/register', [AuthController::class, 'apiRegister']);
    Route::post('/auth/login', [AuthController::class, 'apiLogin']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/refresh', [AuthController::class, 'apiRefresh']);
        Route::post('/auth/logout', [AuthController::class, 'apiLogout']);
        Route::get('/auth/user', function (Request $request) {
            return response()->json($request->user()->load('roles'));
        });

        Route::get('/procedures', [ApiProcedureController::class, 'index']);
        Route::post('/procedures', [ApiProcedureController::class, 'store']);
        Route::get('/procedures/my', [ApiProcedureController::class, 'my']);
        Route::get('/procedures/types', [ApiProcedureController::class, 'types']);
        Route::get('/procedures/statuses', [ApiProcedureController::class, 'statuses']);
        Route::get('/procedures/{procedure}', [ApiProcedureController::class, 'show']);
        Route::get('/procedures/{procedure}/history', [ApiProcedureController::class, 'history']);
        Route::get('/procedures/{procedure}/documents', [ApiProcedureController::class, 'documents']);

        Route::get('/documents', [ApiDocumentController::class, 'index']);
        Route::post('/documents', [ApiDocumentController::class, 'store']);
        Route::get('/documents/{document}', [ApiDocumentController::class, 'show']);

        Route::get('/news', [NewsController::class, 'apiIndex']);
        Route::get('/news/{news}', [NewsController::class, 'apiShow']);

        Route::get('/faqs', [FaqController::class, 'apiIndex']);
        Route::get('/faqs/{faq}', [FaqController::class, 'apiShow']);
        Route::get('/faq-categories', [FaqController::class, 'apiCategories']);
    });
});
