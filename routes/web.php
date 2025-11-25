<?php

// Importa a facade Route, usada para definir as rotas da aplicação.
use Illuminate\Support\Facades\Route;
// Importa Features do Fortify, usado para configurar recursos como 2FA.
use Laravel\Fortify\Features;

// Importa componentes Livewire usados na área de configurações (settings).
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;

// Importa os controllers que serão usados pelas rotas.
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\PedidoController;

// ROTA INICIAL (HOME)
// Quando o usuário acessa "/" (raiz do site), devolve a view 'welcome'.
// A rota recebe o nome 'home'.
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Agrupa um conjunto de rotas que só podem ser acessadas por usuários
// autenticados ('auth') e com e-mail verificado ('verified').
Route::middleware(['auth', 'verified'])->group(function () {

    // ROTA DO DASHBOARD
    // GET /dashboard → chama o método dashboard() do ProdutoController.
    // Nome da rota: 'dashboard'.
    Route::get('/dashboard', [ProdutoController::class, 'dashboard'])->name('dashboard');

    // REDIRECIONAMENTO DE /settings PARA /settings/profile
    Route::redirect('settings', 'settings/profile');

    // Rotas da área de configurações (profile, senha, aparência),
    // usando componentes Livewire em vez de controllers tradicionais.

    // GET /settings/profile → componente Livewire Profile.
    // Nome da rota: profile.edit
    Route::get('settings/profile', Profile::class)->name('profile.edit');

    // GET /settings/password → componente Livewire Password.
    // Nome da rota: user-password.edit
    Route::get('settings/password', Password::class)->name('user-password.edit');

    // GET /settings/appearance → componente Livewire Appearance.
    // Nome da rota: appearance.edit
    Route::get('settings/appearance', Appearance::class)->name('appearance.edit');

    // ROTA PARA CONFIGURAÇÃO DE DOIS FATORES (2FA)
    Route::get('settings/two-factor', TwoFactor::class)
        ->middleware(
            when(
                // Verifica se o Fortify permite gerenciar 2FA
                // e se a opção de confirmar senha está ativa.
                Features::canManageTwoFactorAuthentication()
                && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                // Se sim, aplica o middleware 'password.confirm'
                ['password.confirm'],
                // Senão, não aplica middleware extra.
                [],
            ),
        )
        ->name('two-factor.show');

    // ROTAS RESOURCE PARA PRODUTOS
    // Cria automaticamente rotas RESTful para ProdutoController:
    // index, create, store, show, edit, update e destroy.
    // Ex.: GET /produtos → ProdutoController@index
    //      POST /produtos → ProdutoController@store
    Route::resource('produtos', ProdutoController::class);

    // ROTAS RESOURCE PARA CATEGORIAS
    // Mesmo esquema, mas para CategoriaController.
    Route::resource('categorias', CategoriaController::class);

    // Outro grupo de rotas também protegido por 'auth' e 'verified'
    // (aqui é redundante, porque já estamos dentro de um grupo com esses middlewares,
    //  mas não atrapalha).
    Route::middleware(['auth','verified'])->group(function () {

        // ROTAS RESOURCE PARA PEDIDOS (parcial)
        // Cria somente as rotas index, create e store para PedidoController.
        // - GET /pedidos → index (mostrar carrinho)
        // - GET /pedidos/create → create (listar produtos para comprar)
        // - POST /pedidos → store (adicionar produto ao carrinho)
        Route::resource('pedidos', PedidoController::class)->only(['index','create','store']);

        // ROTA PARA ATUALIZAR UM ITEM DO PEDIDO (carrinho)
        // PUT /itens-pedidos/{item} → PedidoController@updateItem
        // Nome da rota: pedidos.itens.update
        Route::put('itens-pedidos/{item}',    [PedidoController::class, 'updateItem'])
             ->name('pedidos.itens.update');

        // ROTA PARA REMOVER UM ITEM DO PEDIDO
        // DELETE /itens-pedidos/{item} → PedidoController@destroyItem
        // Nome da rota: pedidos.itens.destroy
        Route::delete('itens-pedidos/{item}', [PedidoController::class, 'destroyItem'])
             ->name('pedidos.itens.destroy');

        // ROTA PARA FINALIZAR UM PEDIDO
        // POST /pedidos/{pedido}/finalizar → PedidoController@finalizar
        // Nome da rota: pedidos.finalizar
        Route::post('pedidos/{pedido}/finalizar', [PedidoController::class, 'finalizar'])
             ->name('pedidos.finalizar');

        // ROTA PARA MOSTRAR DETALHES DE UM PRODUTO DENTRO DO FLUXO DE PEDIDO
        // GET /pedidos/produto/{produto} → PedidoController@showProduto
        // Nome da rota: pedidos.produto.show
        Route::get('pedidos/produto/{produto}', [PedidoController::class, 'showProduto'])
             ->middleware(['auth'])  // reforça que precisa estar logado
             ->name('pedidos.produto.show');
    });
});
