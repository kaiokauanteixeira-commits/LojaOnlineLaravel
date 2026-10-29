<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adiciona a categoria aos produtos existentes.
     */
    public function up(): void
    {
        Schema::table('produtos', function (Blueprint $table) {

            // Categoria inicialmente opcional para preservar
            // todos os produtos já cadastrados.
            $table->string('categoria', 50)->nullable();

        });
    }

    /**
     * Desfaz a alteração, se necessário.
     */
    public function down(): void
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->dropColumn('categoria');
        });
    }
};
