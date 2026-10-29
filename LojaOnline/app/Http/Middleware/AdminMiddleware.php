<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Permite acesso somente aos administradores autenticados.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Se o usuário não estiver autenticado,
        // direciona para a página de login.
        if (!auth()->check()) {
            return redirect()->route('admin.login');
        }

        // Se estiver autenticado, mas não for administrador,
        // impede o acesso ao gerenciamento.
        if (!auth()->user()->is_admin) {
            abort(403, 'Acesso permitido somente para administradores.');
        }

        // Usuário autorizado: continua a requisição.
        return $next($request);
    }
}
