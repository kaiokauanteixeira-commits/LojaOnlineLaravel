<?php

namespace App\Http\Controllers;

// Model responsável pelos produtos cadastrados.
use App\Models\Produto;

// Recebe e valida os dados enviados pelos formulários.
use Illuminate\Http\Request;

// Gera o endereço público das imagens dos produtos.
use Illuminate\Support\Facades\Storage;

class CarrinhoController extends Controller
{
    /**
     * Exibe o carrinho.
     *
     * Navegação tradicional: retorna a página carrinho.blade.php.
     * Requisição AJAX: retorna os dados em JSON para o modal.
     */
    public function index(Request $request)
    {
        // Recupera o carrinho armazenado na sessão.
        $carrinho = session()->get('carrinho', []);

        // Calcula o valor total da compra.
        $total = 0;

        foreach ($carrinho as $item) {
            $total += $item['preco'] * $item['quantidade'];
        }

        // O JavaScript utiliza os dados em formato JSON.
        if ($request->expectsJson()) {
            return $this->resumoJson();
        }

        // Preserva a página tradicional do carrinho.
        return view('loja.carrinho', compact('carrinho', 'total'));
    }

    /**
     * Adiciona um produto ao carrinho.
     */
    public function adicionar(Request $request, Produto $produto)
    {
        // Verifica se a quantidade enviada é um inteiro positivo.
        $dados = $request->validate([
            'quantidade' => 'required|integer|min:1',
        ]);

        // Recupera os produtos que já estão no carrinho.
        $carrinho = session()->get('carrinho', []);

        // Identifica o produto selecionado.
        $id = $produto->id;

        // Obtém a quantidade já adicionada anteriormente.
        $quantidadeAtual = $carrinho[$id]['quantidade'] ?? 0;

        // Soma a nova quantidade à quantidade existente.
        $novaQuantidade = $quantidadeAtual + $dados['quantidade'];

        // Impede adicionar mais unidades do que o estoque.
        if ($novaQuantidade > $produto->estoque) {

            $mensagem = 'Quantidade solicitada maior que o estoque disponível.';

            // Retorna erro JSON para o JavaScript.
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $mensagem
                ], 422);
            }

            // Mantém o comportamento dos formulários tradicionais.
            return back()->with('error', $mensagem);
        }

        // Armazena os dados necessários para o carrinho.
        $carrinho[$id] = [
            'id' => $produto->id,
            'nome' => $produto->nome,
            'preco' => $produto->preco,
            'imagem' => $produto->imagem,
            'quantidade' => $novaQuantidade,
        ];

        // Atualiza o carrinho na sessão.
        session()->put('carrinho', $carrinho);

        // Resposta para a interface com modal.
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Produto adicionado ao carrinho!'
            ]);
        }

        // Resposta para a página tradicional.
        return redirect()->route('carrinho.index')
            ->with('success', 'Produto adicionado ao carrinho!');
    }

    /**
     * Atualiza a quantidade de um produto no carrinho.
     */
    public function atualizar(Request $request, Produto $produto)
    {
        // Valida a quantidade informada.
        $dados = $request->validate([
            'quantidade' => 'required|integer|min:1',
        ]);

        // Recupera o carrinho.
        $carrinho = session()->get('carrinho', []);

        // Verifica se o produto realmente está no carrinho.
        if (!isset($carrinho[$produto->id])) {

            $mensagem = 'Produto não encontrado no carrinho.';

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $mensagem
                ], 404);
            }

            return redirect()->route('carrinho.index')
                ->with('error', $mensagem);
        }

        // Não permite quantidade superior ao estoque disponível.
        if ($dados['quantidade'] > $produto->estoque) {

            $mensagem = 'Estoque insuficiente para essa quantidade.';

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $mensagem
                ], 422);
            }

            return back()->with('error', $mensagem);
        }

        // Altera a quantidade do produto selecionado.
        $carrinho[$produto->id]['quantidade'] = $dados['quantidade'];

        // Salva o carrinho atualizado.
        session()->put('carrinho', $carrinho);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Carrinho atualizado com sucesso!'
            ]);
        }

        return redirect()->route('carrinho.index')
            ->with('success', 'Carrinho atualizado com sucesso!');
    }

    /**
     * Remove um produto do carrinho.
     */
    public function remover(Request $request, Produto $produto)
    {
        // Recupera os produtos da sessão.
        $carrinho = session()->get('carrinho', []);

        // Remove o produto selecionado.
        unset($carrinho[$produto->id]);

        // Salva o carrinho sem o produto removido.
        session()->put('carrinho', $carrinho);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Produto removido do carrinho!'
            ]);
        }

        return redirect()->route('carrinho.index')
            ->with('success', 'Produto removido do carrinho!');
    }

    /**
     * Prepara os dados do carrinho para o JavaScript.
     *
     * Retorna os produtos, o valor total e a quantidade
     * total de unidades presentes no carrinho.
     */
    private function resumoJson()
    {
        // Recupera o carrinho atual.
        $carrinho = session()->get('carrinho', []);

        // Inicializa os dados do resumo.
        $itens = [];
        $total = 0;
        $quantidadeTotal = 0;

        foreach ($carrinho as $item) {

            // Calcula o subtotal do produto.
            $subtotal = (float) $item['preco']
                * (int) $item['quantidade'];

            // Acumula o total da compra.
            $total += $subtotal;

            // Conta todas as unidades adicionadas.
            $quantidadeTotal += (int) $item['quantidade'];

            // Prepara os dados que serão exibidos no modal.
            $itens[] = [
                'id' => $item['id'],
                'nome' => $item['nome'],
                'preco' => (float) $item['preco'],
                'quantidade' => (int) $item['quantidade'],

                // Converte o caminho da imagem em uma URL pública.
                'imagem_url' => !empty($item['imagem'])
                    ? Storage::disk('public')->url($item['imagem'])
                    : null,
            ];
        }

        // Envia os dados para a interface da loja.
        return response()->json([
            'itens' => $itens,
            'total' => $total,
            'quantidade_total' => $quantidadeTotal,
        ]);
    }
}
