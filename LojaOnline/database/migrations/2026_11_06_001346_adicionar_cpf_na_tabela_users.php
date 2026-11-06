<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // Adiciona a coluna CPF com 11 caracteres
            // Permite NULL para não afetar usuários já cadastrados
            // Impede que dois usuários tenham o mesmo CPF
            $table->string('cpf', 11)->nullable()->unique();

        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // Remove a restrição de CPF e a coluna
            $table->dropUnique(['cpf']);
            $table->dropColumn('cpf');

        });
    }
};
