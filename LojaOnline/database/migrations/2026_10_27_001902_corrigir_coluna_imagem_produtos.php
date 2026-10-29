<?php

// Importa a classe responsável pelas migrations.
use Illuminate\Database\Migrations\Migration;

// Permite definir a estrutura das colunas.
use Illuminate\Database\Schema\Blueprint;

// Permite consultar e modificar tabelas do banco.
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Corrige a estrutura da tabela produtos.
     * Adiciona a coluna imagem somente se ela não existir.
     */
    public function up(): void
    {
        // Verifica se a coluna imagem já existe.
        if (!Schema::hasColumn('produtos', 'imagem')) {

            // Adiciona a coluna na tabela existente.
            Schema::table('produtos', function (Blueprint $table) {

                // Armazena o caminho da imagem.
                // Permite produtos sem imagem cadastrada.
                $table->string('imagem')->nullable();

            });
        }
    }

    /**
     * Reverte a alteração feita por esta migration.
     */
    public function down(): void
    {
        // Verifica se a coluna existe antes de removê-la.
        if (Schema::hasColumn('produtos', 'imagem')) {

            Schema::table('produtos', function (Blueprint $table) {

                // Remove a coluna imagem ao reverter.
                $table->dropColumn('imagem');

            });
        }
    }
};
