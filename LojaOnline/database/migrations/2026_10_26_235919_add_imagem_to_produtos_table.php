<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adiciona a coluna imagem na tabela produtos.
     * A coluna armazenará o caminho do arquivo da imagem.
     */
    public function up(): void
    {
        Schema::table('produtos', function (Blueprint $table) {

            // Campo opcional para armazenar o caminho da imagem.
            // nullable() permite que produtos antigos continuem sem imagem.
            $table->string('imagem')->nullable();

        });
    }

    /**
     * Reverte a alteração, removendo a coluna imagem.
     */
    public function down(): void
    {
        Schema::table('produtos', function (Blueprint $table) {

            // Remove a coluna caso a migration seja revertida.
            $table->dropColumn('imagem');

        });
    }
};
