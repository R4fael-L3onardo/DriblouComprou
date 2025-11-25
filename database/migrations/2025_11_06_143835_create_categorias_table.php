<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration que cria a tabela 'categorias'.
return new class extends Migration
{
    // Método up(): o que acontece quando aplicamos a migration.
    public function up(): void
    {
        Schema::create('categorias', function (Blueprint $table) {
            // Chave primária auto-incremento (BIGINT).
            $table->id();

            // Nome da categoria (ex.: Camisas, Bonés, Times Europeus etc.).
            $table->string('nome');

            // CHAVE ESTRANGEIRA: usuário que criou a categoria.
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')
                  ->references('id')->on('users')
                  // onDelete('cascade') → se o usuário for apagado,
                  // todas as categorias criadas por ele também serão apagadas.
                  ->onDelete('cascade');
                  
            // created_at e updated_at.
            $table->timestamps();
        });
    }

    // Método down(): o que acontece se a migration for revertida (rollback).
    public function down(): void
    {
        // Apaga a tabela 'categorias' se ela existir.
        Schema::dropIfExists('categorias');
    }
};
