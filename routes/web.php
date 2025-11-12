<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use App\Http\Controllers\ProdutoController; // Importa a classe ProdutoController (atalho pro namespace completo)
use App\Models\Produto;
use App\Http\Controllers\CategoriaController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', function () {
    $totalProdutos = \App\Models\Produto::where('created_by', \Illuminate\Support\Facades\Auth::id())->count();
    return view('dashboard', compact('totalProdutos'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('profile.edit');
    Route::get('settings/password', Password::class)->name('user-password.edit');
    Route::get('settings/appearance', Appearance::class)->name('appearance.edit');

    Route::get('settings/two-factor', TwoFactor::class)
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});

Route::get('/produtos', function () {
    return view('produtos.index'); // Aponta para resources/views/produtos/index.blade.php
});

Route::resource('produtos', ProdutoController::class)
    ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);


Route::resource('produtos', ProdutoController::class)->middleware('auth');

Route::resource('categorias', CategoriaController::class)->middleware('auth');

Route::resource('categorias', CategoriaController::class)
    ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);