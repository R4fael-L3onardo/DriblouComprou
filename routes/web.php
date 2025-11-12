<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;

use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\CategoriaController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

/**
 * Rotas autenticadas (inclui dashboard, settings e CRUDs)
 */
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard -> usa ProdutoController@dashboard para enviar $totalProdutos e $totalCategorias
    Route::get('/dashboard', [ProdutoController::class, 'dashboard'])->name('dashboard');

    // Settings
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

    // CRUDs
    Route::resource('produtos', ProdutoController::class);
    Route::resource('categorias', CategoriaController::class);
});
