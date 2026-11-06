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

        // Remove pontos e traço do CPF antes da validação.
        $cpf = preg_replace('/\D/', '', $request->input('cpf', ''));

        $request->merge([
            'cpf' => $cpf
        ]);

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

    // CPF do cliente.
    'cpf' => [
        'required',
        'digits:11',
        'unique:users,cpf',
        function ($attribute, $value, $fail) {
            if (!$this->validarCpf($value)) {
                $fail('O CPF informado é inválido.');
            }
        },
    ],

            // Endereço de entrega.
            'cep' => ['required', 'string', 'max:9'],
            'logradouro' => ['required', 'string', 'max:255'],
            'numero' => ['required', 'string', 'max:20'],
            'complemento' => ['nullable', 'string', 'max:255'],
            'bairro' => ['required', 'string', 'max:255'],
            'cidade' => ['required', 'string', 'max:255'],
            'estado' => ['required', 'string', 'size:2'],

        ], [

            // Mensagens personalizadas em português.
            'name.required' => 'Informe seu nome completo.',

            'email.required' => 'Informe seu e-mail.',
            'email.email' => 'Informe um endereço de e-mail válido.',
            'email.unique' => 'Este e-mail já está cadastrado.',

            'password.required' => 'Informe uma senha.',
            'password.confirmed' => 'As senhas não coincidem.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',

            'telefone.required' => 'Informe seu telefone.',

            'cpf.required' => 'Informe seu CPF.',
            'cpf.digits' => 'O CPF deve conter 11 números.',
            'cpf.unique' => 'Este CPF já está cadastrado.',

            'cep.required' => 'Informe seu CEP.',
            'logradouro.required' => 'Informe sua rua ou avenida.',
            'numero.required' => 'Informe o número.',
            'bairro.required' => 'Informe seu bairro.',
            'cidade.required' => 'Informe sua cidade.',
            'estado.required' => 'Informe seu estado.',
            'estado.size' => 'O estado deve conter 2 letras.',
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

        // Salva o CPF sem pontos e traço.
        $cliente->cpf = $dados['cpf'];

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


    // ==========================================
    // VALIDAÇÃO DO CPF
    // ==========================================

    // Verifica se o CPF possui dígitos verificadores válidos.
    private function validarCpf(string $cpf): bool
    {
        // Verifica se possui 11 números.
        if (strlen($cpf) != 11) {
            return false;
        }

        // Rejeita CPFs com todos os números iguais.
        if (preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        // Calcula o primeiro dígito verificador.
        $soma = 0;

        for ($i = 0; $i < 9; $i++) {
            $soma += intval($cpf[$i]) * (10 - $i);
        }

        $resto = $soma % 11;
        $digito1 = ($resto < 2) ? 0 : 11 - $resto;

        // Calcula o segundo dígito verificador.
        $soma = 0;

        for ($i = 0; $i < 10; $i++) {
            $soma += intval($cpf[$i]) * (11 - $i);
        }

        $resto = $soma % 11;
        $digito2 = ($resto < 2) ? 0 : 11 - $resto;

        // Compara os dígitos calculados com os informados.
        return $cpf[9] == $digito1 && $cpf[10] == $digito2;
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