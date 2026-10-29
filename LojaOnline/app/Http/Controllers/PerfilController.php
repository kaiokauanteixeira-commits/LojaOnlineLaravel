<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PerfilController extends Controller
{
    /**
     * Atualiza as informações pessoais do usuário logado.
     */
    public function atualizar(Request $request)
    {
        // Recupera o usuário autenticado.
        $usuario = $request->user();

        // Valida as informações enviadas pelo formulário.
        $dados = $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($usuario->id),
            ],

            'telefone' => ['nullable', 'string', 'max:20'],
            'cep' => ['nullable', 'string', 'max:9'],
            'logradouro' => ['nullable', 'string', 'max:255'],
            'numero' => ['nullable', 'string', 'max:20'],
            'complemento' => ['nullable', 'string', 'max:255'],
            'bairro' => ['nullable', 'string', 'max:255'],
            'cidade' => ['nullable', 'string', 'max:255'],
            'estado' => ['nullable', 'string', 'size:2'],
        ]);

        // Padroniza a sigla do estado.
        if (!empty($dados['estado'])) {
            $dados['estado'] = strtoupper($dados['estado']);
        }

        // Atualiza apenas os dados permitidos no Model User.
        $usuario->update($dados);

        return redirect()
            ->route('loja.index')
            ->with('success', 'Informações atualizadas com sucesso!');
    }
}
