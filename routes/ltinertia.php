<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BreadController;
use App\Http\Controllers\Admin\DatabaseController;
use Inertia\Inertia;

Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/', function () {
        return Inertia::render('Admin/Dashboard', [
            'layout' => 'AdminLayout'
        ]);
    })->name('admin.dashboard');
    // Database Manager Routes
    Route::get('/database', [DatabaseController::class, 'index'])->name('admin.database.index');
    Route::get('/database/create', [DatabaseController::class, 'create'])->name('admin.database.create');
    Route::get('/database/{table}', [DatabaseController::class, 'show'])->name('admin.database.show');  
    Route::post('/database', [DatabaseController::class, 'store'])->name('admin.database.store');
    Route::get('/database/{table}/edit', [DatabaseController::class, 'edit'])->name('admin.database.edit');
    Route::put('/database/{table}', [DatabaseController::class, 'update'])->name('admin.database.update');
    Route::delete('/database/{table}', [DatabaseController::class, 'destroy'])->name('admin.database.destroy');
    Route::get('/database/{table}/columns', [DatabaseController::class, 'columns'])->name('admin.database.columns');
    // Bread Manager Routes
    Route::get('/{slug}', [BreadController::class, 'index'])->name('admin.bread.index');
    Route::get('/{slug}/create', [BreadController::class, 'create'])->name('admin.bread.create');
    Route::get('/{slug}/configure', [BreadController::class, 'configure'])->name('admin.bread.configure');
    Route::put('/{slug}/configure', [BreadController::class, 'saveConfiguration'])->name('admin.bread.configure.save');
    Route::post('/{slug}', [BreadController::class, 'store'])->name('admin.bread.store');
    Route::get('/{slug}/{id}', [BreadController::class, 'show'])->name('admin.bread.show');
    Route::get('/{slug}/{id}/edit', [BreadController::class, 'edit'])->name('admin.bread.edit');
    Route::put('/{slug}/{id}', [BreadController::class, 'update'])->name('admin.bread.update');
    Route::delete('/{slug}/{id}', [BreadController::class, 'destroy'])->name('admin.bread.destroy');
    
});