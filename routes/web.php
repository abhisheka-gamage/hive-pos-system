<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Web\V1\BatchController;
use App\Http\Controllers\Web\V1\Navigation\NavHeaderController;
use App\Http\Controllers\Web\V1\Navigation\NavItemController;
use App\Http\Controllers\Web\V1\Navigation\NavSubItemController;
use App\Http\Controllers\Web\V1\PermissionController;
use App\Http\Controllers\Web\V1\ProductController;
use App\Http\Controllers\Web\V1\ProductRetailerController;
use App\Http\Controllers\Web\V1\RoleController;
use App\Http\Controllers\Web\V1\StockController;
use App\Http\Controllers\Web\V1\UserController;
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
            Route::post('/filter', [RoleController::class, 'filter']);
            Route::post('/{role}', [RoleController::class, 'show']);
            Route::post('/{role}/update', [RoleController::class, 'update']);
        });

        Route::prefix('user')->group(function(){
            Route::post('/index', [UserController::class, 'index']);
            Route::post('/save', [UserController::class, 'store']);
            Route::post('/{user}', [UserController::class, 'show']);
            Route::post('/{user}/update', [UserController::class, 'update']);
        });


    });
    Route::prefix('products')->group(function(){
        Route::prefix('retailers')->group(function(){
            Route::post('/index', [ProductRetailerController::class, 'index']);
            Route::post('/filter', [ProductRetailerController::class, 'filter']);
            Route::post('/save', [ProductRetailerController::class, 'store']);
            Route::post('/{user}', [ProductRetailerController::class, 'show']);
            Route::post('/{user}/update', [ProductRetailerController::class, 'update']);
        });

        Route::prefix('product')->group(function(){
            Route::post('/index', [ProductController::class, 'index']);
            Route::post('/filter', [ProductController::class, 'filter']);
            Route::post('/save', [ProductController::class, 'store']);
            Route::post('/{user}', [ProductController::class, 'show']);
            Route::post('/{user}/update', [ProductController::class, 'update']);
        });

        Route::prefix('batches')->group(function(){
            Route::post('/index', [BatchController::class, 'index']);
            Route::post('/filter', [BatchController::class, 'filter']);
            Route::post('/get_code', [BatchController::class, 'get_code']);
            Route::post('/save', [BatchController::class, 'store']);
            Route::post('/{user}', [BatchController::class, 'show']);
            Route::post('/{user}/update', [BatchController::class, 'update']);
        });

        Route::prefix('stocks')->group(function(){
            Route::post('/sample', [StockController::class, 'sample']);
            Route::post('/upload', [StockController::class, 'upload']);
            Route::post('/index', [StockController::class, 'index']);
        });
    });



    require __DIR__.'/router.php';
});

require __DIR__.'/auth.php';
