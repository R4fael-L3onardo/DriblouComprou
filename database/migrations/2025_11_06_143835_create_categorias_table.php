<?php

// Importa a classe responsável pelas migrações do Laravel.
use Illuminate\Database\Migrations\Migration;

// Importa a classe Blueprint, que permite definir as colunas da tabela.
use Illuminate\Database\Schema\Blueprint;

// Importa a facade Schema, usada para criar e manipular tabelas no banco.
use Illuminate\Support\Facades\Schema;

// Retorna uma classe anônima que estende Migration.
// Essa classe terá os métodos up() e down(), usados pelo Laravel
// para rodar (migrate) e desfazer (rollback) essa migração.
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Esse método é executado quando rodamos:
     * php artisan migrate
     *
     * Aqui definimos como a tabela "categorias" será criada.
     */
    public function up(): void
    {
        // Cria uma nova tabela chamada 'categorias' no banco de dados.
        Schema::create('categorias', function (Blueprint $table) {
            // Cria uma coluna 'id' do tipo BIGINT auto-incremento
            // e define como chave primária da tabela.
            $table->id();

            // Cria uma coluna 'nome' do tipo VARCHAR(255),
            // onde será armazenado o nome da categoria.
            // Ex.: "Camisas", "Chuteiras", "Acessórios".
            $table->string('nome');

            // Cria uma coluna 'descricao' do tipo TEXT (texto longo).
            // Essa coluna é opcional (nullable), ou seja, pode ser NULL.
            // Serve para guardar uma descrição mais detalhada da categoria.
            $table->text('descricao')->nullable();

            // Cria uma coluna 'created_by' do tipo UNSIGNED BIGINT.
            // Essa coluna vai armazenar o ID do usuário que criou a categoria
            // (chave estrangeira para a tabela 'users').
            $table->unsignedBigInteger('created_by');

            // Define a chave estrangeira para 'created_by':
            // - 'created_by' referencia a coluna 'id' na tabela 'users'.
            // - onDelete('cascade') significa que, se o usuário for deletado,
            //   todas as categorias criadas por ele também serão excluídas automaticamente.
            $table->foreign('created_by')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
            
            // Cria as colunas 'created_at' e 'updated_at' automaticamente.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * Esse método é executado quando rodamos:
     * php artisan migrate:rollback
     *
     * Ele desfaz o que foi feito no método up(), ou seja,
     * remove a tabela 'categorias' do banco de dados.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorias');
    }
};
