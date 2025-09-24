<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Web\V1\Navigation\NavHeaderController;
use App\Http\Controllers\Web\V1\Navigation\NavItemController;
use App\Http\Controllers\Web\V1\Navigation\NavSubItemController;
use App\Http\Controllers\Web\V1\PermissionController;
use App\Http\Controllers\Web\V1\RoleController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('permissions')->group(function(){
        Route::post('/index', [PermissionController::class, 'index']);

        Route::prefix('headers')->group(function(){
            Route::post('/index', [NavHeaderController::class, 'index']);
            Route::post('/save', [NavHeaderController::class, 'store']);
            Route::post('/filter', [NavHeaderController::class, 'filter']);
        });

        Route::prefix('items')->group(function(){
            Route::post('/index', [NavItemController::class, 'index']);
            Route::post('/save', [NavItemController::class, 'store']);
            Route::post('/filter', [NavItemController::class, 'filter']);
        });

        Route::prefix('sub_items')->group(function(){
            Route::post('/index', [NavSubItemController::class, 'index']);
            Route::post('/save', [NavSubItemController::class, 'store']);
            Route::post('/filter', [NavSubItemController::class, 'filter']);
        });

        Route::prefix('types')->group(function(){
            Route::post('/filter', [PermissionController::class, 'type_filter']);
        });
    });

    Route::prefix('users')->group(function(){
        Route::prefix('roles')->group(function(){
            Route::post('/index', [RoleController::class, 'index']);
            Route::post('/save', [RoleController::class, 'store']);
            Route::post('/{role}', [RoleController::class, 'show']);
            Route::post('/{role}/update', [RoleController::class, 'update']);
        });
    });



    require __DIR__.'/router.php';
});

require __DIR__.'/auth.php';
