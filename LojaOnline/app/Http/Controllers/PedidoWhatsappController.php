<?php
namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;

class PedidoWhatsappController extends Controller
{
    /**
     * Gera um link do WhatsApp com o pedido formatado.
     *
     * Esta função não registra uma venda nem altera o estoque.
     * O pedido será confirmado posteriormente pelo atendimento.
     */
    private function gerarLink(array $itens): string
    {
        $numero = config('services.brisagamer.whatsapp');

        $mensagem = "🎮 *NOVO PEDIDO - BRISAGAMER*\n\n";
        $mensagem .= "Olá! Gostaria de realizar o seguinte pedido:\n\n";

        // ==========================================
        // DADOS DO CLIENTE E ENDEREÇO DE ENTREGA
        // ==========================================

        // Recupera o usuário autenticado, caso exista.
        $cliente = auth()->user();

        if ($cliente) {

            // Nome e telefone cadastrados.
            $mensagem .= "👤 *DADOS DO CLIENTE*\n";
            $mensagem .= "Nome: {$cliente->name}\n";
            $mensagem .= "Telefone: " .
                ($cliente->telefone ?: 'Não informado') . "\n\n";

            // Verifica se o cliente possui endereço cadastrado.
            if (
                $cliente->logradouro &&
                $cliente->numero &&
                $cliente->cidade
            ) {

                $mensagem .= "📍 *ENDEREÇO DE ENTREGA*\n";

                $mensagem .= "Endereço: {$cliente->logradouro}, {$cliente->numero}\n";

                // Complemento é opcional.
                if ($cliente->complemento) {
                    $mensagem .= "Complemento: {$cliente->complemento}\n";
                }

                $mensagem .= "Bairro: " .
                    ($cliente->bairro ?: 'Não informado') . "\n";

                $mensagem .= "Cidade: {$cliente->cidade}\n";

                $mensagem .= "Estado: " .
                    ($cliente->estado ?: 'Não informado') . "\n";

                $mensagem .= "CEP: " .
                    ($cliente->cep ?: 'Não informado') . "\n\n";

            } else {

                // Clientes antigos podem não ter endereço cadastrado.
                $mensagem .= "📍 Endereço de entrega: A confirmar\n\n";
            }

        } else {

            // Mantém a possibilidade de comprar sem cadastro.
            $mensagem .= "👤 Cliente sem cadastro\n";
            $mensagem .= "📍 Endereço de entrega: A informar\n\n";
        }

        $total = 0;

        foreach ($itens as $indice => $item) {
            $produto = $item['produto'];
            $quantidade = $item['quantidade'];

            $preco = (float) $produto->preco;
            $subtotal = round($preco * $quantidade, 2);

            $total += $subtotal;

            $mensagem .= ($indice + 1) . ". *{$produto->nome}*\n";
            $mensagem .= "Quantidade: {$quantidade}\n";
            $mensagem .= "Preço unitário: R$ " .
                number_format($preco, 2, ',', '.') . "\n";
            $mensagem .= "Subtotal: R$ " .
                number_format($subtotal, 2, ',', '.') . "\n\n";
        }

        $mensagem .= "💰 *TOTAL: R$ " .
            number_format($total, 2, ',', '.') . "*\n\n";

        $mensagem .= "Gostaria de confirmar a disponibilidade, ";
        $mensagem .= "as formas de pagamento e a entrega.";

        return 'https://wa.me/' . $numero .
            '?text=' . rawurlencode($mensagem);
    }

    /**
     * Comprar agora:
     * recebe um produto e a quantidade escolhida.
     */
    public function comprar(Request $request, Produto $produto)
    {
        $dados = $request->validate([
            'quantidade' => ['required', 'integer', 'min:1'],
        ]);

        $quantidade = (int) $dados['quantidade'];

        // Impede comprar uma quantidade acima do estoque.
        if ($quantidade > $produto->estoque) {
            return back()->withErrors([
                'quantidade' => 'Quantidade indisponível em estoque.',
            ]);
        }

        $url = $this->gerarLink([
            [
                'produto' => $produto,
                'quantidade' => $quantidade,
            ],
        ]);

        return redirect()->away($url);
    }

    /**
     * Finalizar carrinho:
     * recupera os itens da sessão e consulta os preços atuais.
     */
    public function finalizar(Request $request)
    {
        $carrinho = session('carrinho', []);

        if (empty($carrinho)) {
            return back()->withErrors([
                'carrinho' => 'Seu carrinho está vazio.',
            ]);
        }

        $itens = [];

        foreach ($carrinho as $id => $item) {
            $produto = Produto::find($id);

            if (!$produto) {
                return back()->withErrors([
                    'carrinho' => 'Um produto do carrinho não está mais disponível.',
                ]);
            }

            $quantidade = (int) ($item['quantidade'] ?? 0);

            if ($quantidade < 1 || $quantidade > $produto->estoque) {
                return back()->withErrors([
                    'carrinho' =>
                        "Estoque insuficiente para {$produto->nome}.",
                ]);
            }

            $itens[] = [
                'produto' => $produto,
                'quantidade' => $quantidade,
            ];
        }

        return redirect()->away($this->gerarLink($itens));
    }
}
