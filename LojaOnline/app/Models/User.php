<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// Campos permitidos para cadastro e edição de usuários.
#[Fillable([
    'name',
    'email',
    'password',

    // Informações de contato.
    'telefone',

    // Endereço de entrega.
    'cep',
    'logradouro',
    'numero',
    'complemento',
    'bairro',
    'cidade',
    'estado',
])]

// Campos que não devem aparecer em respostas serializadas.
#[Hidden(['password', 'remember_token'])]

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Define a conversão automática dos atributos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',

            // Identifica se o usuário é administrador.
            'is_admin' => 'boolean',
        ];
    }
}
