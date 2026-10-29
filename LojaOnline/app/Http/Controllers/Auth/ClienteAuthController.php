<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ClienteAuthController extends Controller
{
    /**
     * Exibe o formulário de cadastro.
     */
    public function mostrarCadastro()
    {
        // Usuários autenticados não precisam se cadastrar novamente.
        if (Auth::check()) {
            return redirect()->route('loja.index');
        }

        return view('auth.cliente-cadastro');
    }


/**
 * Cadastra um novo cliente com informações pessoais
 * e endereço de entrega.
 */
public function cadastrar(Request $request)
{
    // Impede que um usuário autenticado cadastre outra conta.
    if (Auth::check()) {
        return redirect()->route('loja.index');
    }

    // ==========================================
    // VALIDAÇÃO DOS DADOS DO CLIENTE
    // ==========================================

    $dados = $request->validate([

        // Informações pessoais.
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        'password' => ['required', 'confirmed', Password::min(8)],

        // Telefone de contato.
        'telefone' => ['required', 'string', 'max:20'],

        // Endereço de entrega.
        'cep' => ['required', 'string', 'max:9'],
        'logradouro' => ['required', 'string', 'max:255'],
        'numero' => ['required', 'string', 'max:20'],
        'complemento' => ['nullable', 'string', 'max:255'],
        'bairro' => ['required', 'string', 'max:255'],
        'cidade' => ['required', 'string', 'max:255'],
        'estado' => ['required', 'string', 'size:2'],

    ]);

        // ==========================================
        // CRIAÇÃO DA CONTA
        // ==========================================

        // Cria um novo usuário.
        $cliente = new User();

        // Informações de acesso.
        $cliente->name = $dados['name'];
        $cliente->email = $dados['email'];
        $cliente->password = Hash::make($dados['password']);

        // Informações de contato.
        $cliente->telefone = $dados['telefone'];

        // Endereço de entrega.
        $cliente->cep = $dados['cep'];
        $cliente->logradouro = $dados['logradouro'];
        $cliente->numero = $dados['numero'];

        // Complemento é opcional.
        $cliente->complemento = $dados['complemento'] ?? null;

        $cliente->bairro = $dados['bairro'];
        $cliente->cidade = $dados['cidade'];
        $cliente->estado = strtoupper($dados['estado']);

        // Todo cadastro público cria um cliente comum.
        // Não permite conceder acesso administrativo.
        $cliente->is_admin = false;

        // Salva todas as informações no banco de dados.
        $cliente->save();

        // ==========================================
        // LOGIN AUTOMÁTICO APÓS O CADASTRO
        // ==========================================

        Auth::login($cliente);

        // Regenera a sessão para proteger a autenticação.
        $request->session()->regenerate();

        // Direciona o cliente para a loja.
        return redirect()->route('loja.index')
            ->with('Sucesso', 'Cadastro realizado com sucesso!');
    }


    /**
     * Exibe o formulário de login do cliente.
     */
    public function mostrarLogin()
    {
        if (Auth::check()) {
            return redirect()->route('loja.index');
        }

        return view('auth.cliente-login');
    }


    /**
     * Realiza o login de clientes e administradores.
     * Ambos são direcionados para a loja pública.
     */
    public function entrar(Request $request)
    {
        // Valida os dados enviados pelo formulário.
        $dados = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Autentica qualquer usuário com credenciais válidas.
        // O campo is_admin não é usado como restrição aqui.
        if (!Auth::attempt($dados)) {
            return back()
                ->withErrors([
                    'email' => 'E-mail ou senha inválidos.',
                ])
                ->onlyInput('email');
        }

        // Regenera a sessão após autenticar.
        $request->session()->regenerate();

        // Cliente e administrador visualizam a mesma loja.
        return redirect()->route('loja.index')
            ->with('Sucesso', 'Login realizado com sucesso!');
    }


    /**
     * Encerra a sessão do cliente.
     */
    public function sair(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('loja.index')
            ->with('Sucesso', 'Você saiu da sua conta.');
    }
}
