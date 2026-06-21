<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DocumentController;
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

        Route::get('/procedures', [ProcedureController::class, 'apiIndex']);
        Route::post('/procedures', [ProcedureController::class, 'apiStore']);
        Route::get('/procedures/my', [ProcedureController::class, 'apiMy']);
        Route::get('/procedures/types', [ProcedureController::class, 'apiTypes']);
        Route::get('/procedures/statuses', [ProcedureController::class, 'apiStatuses']);
        Route::get('/procedures/{procedure}', [ProcedureController::class, 'apiShow']);
        Route::get('/procedures/{procedure}/history', [ProcedureController::class, 'apiHistory']);
        Route::get('/procedures/{procedure}/documents', [ProcedureController::class, 'apiDocuments']);

        Route::get('/documents', [DocumentController::class, 'apiIndex']);
        Route::post('/documents', [DocumentController::class, 'apiStore']);
        Route::get('/documents/{document}', [DocumentController::class, 'apiShow']);

        Route::get('/news', [NewsController::class, 'apiIndex']);
        Route::get('/news/{news}', [NewsController::class, 'apiShow']);

        Route::get('/faqs', [FaqController::class, 'apiIndex']);
        Route::get('/faqs/{faq}', [FaqController::class, 'apiShow']);
        Route::get('/faq-categories', [FaqController::class, 'apiCategories']);
    });
});
