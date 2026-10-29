<?php

// Define o namespace do Controller.
namespace App\Http\Controllers;

// Importa o Model dos produtos.
use App\Models\Produto;

// Controller responsável pelas páginas dos clientes.
class LojaController extends Controller
{
    // Exibe o catálogo público da loja.
    public function index()
    {
        // Busca somente produtos com estoque disponível.
        $produtos = Produto::where('estoque', '>', 0)
            ->latest()
            ->get();

        // Envia os produtos para a página do cliente.
        return view('loja.index', compact('produtos'));
    }

        /**
     * Exibe os detalhes de um produto específico.
     *
     * O Laravel localiza automaticamente o produto pelo ID
     * informado na URL, utilizando Route Model Binding.
     */
    public function show(Produto $produto)
    {
        // Impede a exibição de produtos sem estoque.
        abort_if($produto->estoque <= 0, 404);

        // Envia o produto selecionado para a página de detalhes.
        return view('loja.produto', compact('produto'));
    }
}
