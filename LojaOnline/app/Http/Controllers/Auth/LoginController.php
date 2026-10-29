<?php
namespace App\Http\Controllers\Auth;

// Controller base do Laravel.
use App\Http\Controllers\Controller;

// Recebe e valida os dados enviados pelo formulário.
use Illuminate\Http\Request;

// Gerencia a autenticação dos usuários.
use Illuminate\Support\Facades\Auth;

// Permite consultar usuários cadastrados.
use App\Models\User;

// Permite validar senhas com segurança.
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * Exibe a página de login administrativo.
     */
    public function mostrarLogin()
    {
        // Usuários autenticados como administradores
        // são direcionados diretamente ao painel.
        if (Auth::check() && Auth::user()->is_admin) {
            return redirect()->route('admin.index');
        }

        // Exibe o formulário de login.
        return view('auth.login');
    }

    /**
     * Processa o login administrativo.
     */
    public function entrar(Request $request)
    {
        // Valida os dados recebidos do formulário.
        $dados = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Busca o usuário pelo e-mail informado.
        $usuario = User::where('email', $dados['email'])->first();

        // Verifica se o usuário existe, é administrador
        // e informou a senha correta.
        if (
            !$usuario ||
            !$usuario->is_admin ||
            !Hash::check($dados['password'], $usuario->password)
        ) {
            return back()
                ->withErrors([
                    'email' => 'E-mail ou senha inválidos.'
                ])
                ->onlyInput('email');
        }

        // Autentica o administrador utilizando a sessão do Laravel.
        Auth::login($usuario);

        // Regenera a sessão para evitar session fixation.
        $request->session()->regenerate();

        // Redireciona para o painel administrativo.
        return redirect()->route('admin.index')
            ->with('success', 'Login realizado com sucesso!');
    }

    /**
     * Encerra a sessão do administrador.
     */
    public function sair(Request $request)
    {
        // Desconecta o usuário autenticado.
        Auth::logout();

        // Invalida a sessão anterior.
        $request->session()->invalidate();

        // Gera um novo token de segurança CSRF.
        $request->session()->regenerateToken();

        // Retorna para a loja pública.
        return redirect()->route('loja.index')
            ->with('Sucesso', 'Você saiu da conta.');
    }
}
