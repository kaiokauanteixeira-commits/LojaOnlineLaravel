<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\LojaController;
use App\Http\Controllers\CarrinhoController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\Auth\ClienteAuthController;
use App\Http\Controllers\PedidoWhatsappController;
use App\Http\Controllers\PerfilController;

    // =====================================================
    // LOJA PÚBLICA
    // =====================================================

    // Qualquer visitante pode visualizar os produtos.
    Route::get('/loja', [LojaController::class, 'index'])
        ->name('loja.index');

    // Exibe os detalhes de um produto.
    Route::get('/loja/produto/{produto}', [LojaController::class, 'show'])
        ->name('loja.produto');


    // =====================================================
    // CARRINHO DE COMPRAS - ACESSO PÚBLICO
    // =====================================================

    // Mantemos o carrinho disponível sem login obrigatório.
    Route::get('/carrinho', [CarrinhoController::class, 'index'])
        ->name('carrinho.index');

    // Adiciona produtos ao carrinho.
    Route::post('/carrinho/adicionar/{produto}', [CarrinhoController::class, 'adicionar'])
        ->name('carrinho.adicionar');

    // Atualiza a quantidade de produtos.
    Route::patch('/carrinho/atualizar/{produto}', [CarrinhoController::class, 'atualizar'])
        ->name('carrinho.atualizar');

    // Remove produtos do carrinho.
    Route::delete('/carrinho/remover/{produto}', [CarrinhoController::class, 'remover'])
        ->name('carrinho.remover');


    // =====================================================
    // LOGIN ADMINISTRATIVO
    // =====================================================

    // Exibe a tela de login.
    Route::get('/admin/login', [LoginController::class, 'mostrarLogin'])
        ->name('admin.login');

    // Processa o login, limitando tentativas.
    Route::post('/admin/login', [LoginController::class, 'entrar'])
        ->middleware('throttle:5,1')
        ->name('admin.login.entrar');

    // Encerra a sessão do usuário autenticado.
    Route::post('/admin/logout', [LoginController::class, 'sair'])
        ->middleware('auth')
        ->name('admin.logout');


    // =====================================================
    // PAINEL ADMINISTRATIVO - ACESSO RESTRITO
    // =====================================================

    // Todas as rotas deste grupo exigem administrador.
    Route::middleware([AdminMiddleware::class])->group(function ()
    {

        // Painel de gerenciamento de produtos.
        // Mantemos a rota original e adicionamos admin.index.
        Route::get('/', [ProdutoController::class, 'index'])
            ->name('produtos.index');

        // Cadastra um novo produto.
        Route::post('/produtos', [ProdutoController::class, 'store'])
            ->name('produtos.store');

        // Atualiza os dados de um produto.
        Route::put('/produtos/{produto}', [ProdutoController::class, 'update'])
            ->name('produtos.update');

        // Exclui um produto.
        Route::delete('/produtos/{produto}', [ProdutoController::class, 'destroy'])
            ->name('produtos.destroy');
    });

    // Nome adicional para o redirecionamento do login.
    // Usa o mesmo Controller e a mesma proteção.
    Route::get('/admin', [ProdutoController::class, 'index'])
        ->middleware(AdminMiddleware::class)
        ->name('admin.index');


    // =====================================================
    // AUTENTICAÇÃO DE CLIENTES
    // =====================================================

    // Exibe o formulário de cadastro.
    Route::get('/cadastro', [ClienteAuthController::class, 'mostrarCadastro'])
        ->name('cliente.cadastro');

    // Processa o cadastro.
    Route::post('/cadastro', [ClienteAuthController::class, 'cadastrar'])
        ->middleware('throttle:5,1')
        ->name('cliente.cadastrar');

    // Exibe o login de clientes.
    Route::get('/entrar', [ClienteAuthController::class, 'mostrarLogin'])
        ->name('cliente.login');

    // Processa o login.
    Route::post('/entrar', [ClienteAuthController::class, 'entrar'])
        ->middleware('throttle:5,1')
        ->name('cliente.entrar');

    // Encerra a sessão do cliente.
    Route::post('/cliente/sair', [ClienteAuthController::class, 'sair'])
        ->middleware('auth')
        ->name('cliente.sair');


    // ==========================================
    // COMPRAS PELO WHATSAPP - BRISAGAMER
    // ==========================================

    // Compra direta de um único produto.
    Route::post('/comprar/{produto}', [
        PedidoWhatsappController::class,
        'comprar'
    ])->name('pedido.comprar');

    // Finalização dos produtos do carrinho.
    Route::post('/finalizar-compra', [
        PedidoWhatsappController::class,
        'finalizar'
    ])->name('pedido.finalizar');


    // ==========================================
    // CONFIGURAÇÕES DA CONTA DO CLIENTE
    // ==========================================

    // Somente usuários autenticados podem atualizar a própria conta.
    Route::middleware('auth')->group(function () {

        Route::put('/minha-conta', [PerfilController::class, 'atualizar'])
            ->name('perfil.atualizar');

    });

