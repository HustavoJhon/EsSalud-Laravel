<?php

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ProcedureController;
use Illuminate\Support\Facades\Route;

// Landing page (public, shows different content for auth/guest)
Route::get('/', function () {
    if (Auth::check()) {
        return view('home');
    }
    return view('landing');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/forgot-password', [AuthController::class, 'forgotPasswordShow'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'forgotPasswordSend'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'resetPasswordShow'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPasswordUpdate'])->name('password.update');
});

Route::get('/faq', [FaqController::class, 'index'])->name('faq.index');
Route::post('/faq/{faq}/helpful', [FaqController::class, 'helpful'])->name('faq.helpful');
Route::post('/faq/{faq}/not-helpful', [FaqController::class, 'notHelpful'])->name('faq.not-helpful');
Route::get('/news', [NewsController::class, 'index'])->name('news.index');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::post('/password/change', [AuthController::class, 'changePassword'])->name('password.change');

    Route::prefix('procedures')->name('procedures.')->group(function () {
        Route::get('/', [ProcedureController::class, 'index'])->name('index');
        Route::get('/create', [ProcedureController::class, 'create'])->name('create');
        Route::post('/', [ProcedureController::class, 'store'])->name('store');
        Route::get('/{procedure}', [ProcedureController::class, 'show'])->name('show');
        Route::put('/{procedure}', [ProcedureController::class, 'update'])->name('update');
        Route::post('/{procedure}/submit', [ProcedureController::class, 'submit'])->name('submit');
        Route::post('/{procedure}/approve', [ProcedureController::class, 'approve'])->name('approve');
        Route::post('/{procedure}/reject', [ProcedureController::class, 'reject'])->name('reject');
        Route::post('/{procedure}/request-subsanacion', [ProcedureController::class, 'requestSubsanacion'])->name('request-subsanacion');
        Route::post('/{procedure}/subsanar', [ProcedureController::class, 'subsanar'])->name('subsanar');
        Route::post('/{procedure}/assign', [ProcedureController::class, 'assign'])->name('assign');
        Route::post('/{procedure}/comment', [ProcedureController::class, 'storeComment'])->name('comment');
    });

    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/', [DocumentController::class, 'index'])->name('index');
        Route::get('/{document}', [DocumentController::class, 'show'])->name('show');
        Route::post('/', [DocumentController::class, 'store'])->name('store');
        Route::delete('/{document}', [DocumentController::class, 'destroy'])->name('destroy');
        Route::post('/{document}/validate', [DocumentController::class, 'validateDoc'])->name('validate');
    });

    Route::prefix('news')->name('news.')->group(function () {
        Route::get('/create', [NewsController::class, 'create'])->name('create');
        Route::post('/', [NewsController::class, 'store'])->name('store');
        Route::get('/{news}/edit', [NewsController::class, 'edit'])->name('edit');
        Route::put('/{news}', [NewsController::class, 'update'])->name('update');
        Route::delete('/{news}', [NewsController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('index');
        Route::post('/message', [ChatController::class, 'sendMessage'])->name('message');
        Route::get('/sessions', [ChatController::class, 'getSessions'])->name('sessions');
        Route::get('/history/{session}', [ChatController::class, 'getHistory'])->name('history');
        Route::delete('/session/{session}', [ChatController::class, 'deleteSession'])->name('delete');
        Route::post('/feedback/{message}', [ChatController::class, 'feedback'])->name('feedback');
    });

    // Admin routes (SADM only)
    Route::prefix('admin')->name('admin.')->middleware('can:manage-users')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
        Route::resource('roles', RoleController::class)->except(['show']);
    });
});

// Public news detail — defined last to avoid conflict with /news/create
Route::get('/news/{news}', [NewsController::class, 'show'])->name('news.show');
