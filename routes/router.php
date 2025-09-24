<?php
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
