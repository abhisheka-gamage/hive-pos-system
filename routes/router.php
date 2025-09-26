<?php
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/settings', fn()=> Inertia::render('Settings'))->name('settings-view');

Route::prefix('permissions')->group(function(){
    Route::prefix('headers')->group(function(){
        Route::get('/index', fn()=> Inertia::render('permissions/headers/Index'))->name('nav_header-view');
        Route::get('/create', fn()=> Inertia::render('permissions/headers/Create'))->name('nav_header-create');
    });

    Route::prefix('items')->group(function(){
        Route::get('/index', fn()=> Inertia::render('permissions/items/Index'))->name('nav_item-view');
        Route::get('/create', fn()=> Inertia::render('permissions/items/Create'))->name('nav_item-create');
    });

    Route::prefix('sub_items')->group(function(){
        Route::get('/index', fn()=> Inertia::render('permissions/subitems/Index'))->name('nav_sub_item-view');
        Route::get('/create', fn()=> Inertia::render('permissions/subitems/Create'))->name('nav_sub_item-create');
    });

});
Route::prefix('users')->group(function(){
    Route::prefix('roles')->group(function(){
        Route::get('/index', fn()=> Inertia::render('users/roles/Index'))->name('user_roles-view');
        Route::get('/create', fn()=> Inertia::render('users/roles/Create'))->name('user_roles-create');
        Route::get('/edit/{id}', fn($id) =>
            Inertia::render('users/roles/Edit', [
                'id' => $id,
            ])
        )->name('user_roles-edit');
    });
    Route::prefix('user')->group(function(){
        Route::get('/index', fn()=> Inertia::render('users/users/Index'))->name('users-view');
        Route::get('/create', fn()=> Inertia::render('users/users/Create'))->name('users-create');
        Route::get('/edit/{id}', fn($id) =>
            Inertia::render('users/users/Edit', [
                'id' => $id,
            ])
        )->name('users-edit');
    });
});

Route::prefix('products')->group(function(){
    Route::prefix('retailers')->group(function(){
        Route::get('/index', fn()=> Inertia::render('products/retailers/Index'))->name('product_retailers-view');
        Route::get('/create', fn()=> Inertia::render('products/retailers/Create'))->name('product_retailers-create');
        Route::get('/edit/{id}', fn($id) =>
            Inertia::render('products/retailers/Edit', [
                'id' => $id,
            ])
        )->name('product_retailers-edit');
    });

    Route::prefix('product')->group(function(){
        Route::get('/index', fn()=> Inertia::render('products/product/Index'))->name('products-view');
        Route::get('/create', fn()=> Inertia::render('products/product/Create'))->name('products-create');
        Route::get('/edit/{id}', fn($id) =>
            Inertia::render('products/product/Edit', [
                'id' => $id,
            ])
        )->name('products-edit');
    });

    Route::prefix('batches')->group(function(){
        Route::get('/index', fn()=> Inertia::render('products/batches/Index'))->name('product_batches-view');
        Route::get('/create', fn()=> Inertia::render('products/batches/Create'))->name('product_batches-create');
        Route::get('/edit/{id}', fn($id) =>
            Inertia::render('products/batches/Edit', [
                'id' => $id,
            ])
        )->name('product_batches-edit');
    });

    Route::prefix('stocks')->group(function(){
        Route::get('/index', fn()=> Inertia::render('products/stocks/Index'))->name('product_stocks-view');
        Route::get('/create', fn()=> Inertia::render('products/stocks/Create'))->name('product_stocks-create');
        Route::get('/edit/{id}', fn($id) =>
            Inertia::render('products/stocks/Edit', [
                'id' => $id,
            ])
        )->name('product_stocks-edit');
    });
});

Route::prefix('sales')->group(function(){
    Route::get('/', fn()=> Inertia::render('sales/invoices/Create'))->name('sales-create');
});
