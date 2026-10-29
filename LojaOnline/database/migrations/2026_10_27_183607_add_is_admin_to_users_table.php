<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adiciona o campo que identifica administradores.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // false: usuário comum.
            // true: administrador.
            //
            // O valor padrão false impede que novos usuários
            // recebam privilégios administrativos automaticamente.
            $table->boolean('is_admin')->default(false);
        });
    }

    /**
     * Desfaz a alteração caso seja necessário rollback.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // Remove somente a coluna adicionada nesta migration.
            $table->dropColumn('is_admin');
        });
    }
};
