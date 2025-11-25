<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration que cria a tabela 'produtos'.
return new class extends Migration
{
    // Método up(): executado quando aplicamos a migration.
    public function up(): void
    {
        Schema::create('produtos', function (Blueprint $table) {
            // Chave primária auto-incremento (BIGINT).
            $table->id();

            // CHAVE ESTRANGEIRA: categoria_id (pode ser nula).
            $table->unsignedBigInteger('categoria_id')->nullable();
            $table->foreign('categoria_id')
                  ->references('id')->on('categorias')
                  // onDelete('set null') → se a categoria for apagada,
                  // o campo categoria_id do produto vira NULL (produto fica "sem categoria").
                  ->onDelete('set null');

            // Nome do produto (string sem limite de caracteres definido, default 255).
            $table->string('nome');

            // Preço do produto: decimal com 10 dígitos, 2 casas decimais.
            $table->decimal('preco', 10, 2);

            // Tamanho do produto (até 5 caracteres, ex.: P, M, G, 42, etc.).
            $table->string('tamanho', 5);

            // Caminho da imagem do produto. Pode ser nulo (produto sem imagem).
            $table->string('imagem')->nullable();

            // Quantidade em estoque. Inteiro, começa em 0 por padrão.
            $table->integer('estoque')->default(0);

            // CHAVE ESTRANGEIRA: created_by, indicando qual usuário criou o produto.
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')
                  ->references('id')->on('users')
                  // onDelete('cascade') → se o usuário for apagado,
                  // todos os produtos criados por ele também são apagados.
                  ->onDelete('cascade');

            // created_at e updated_at.
            $table->timestamps();
        });
    }

    // Método down(): executado quando damos rollback na migration.
    public function down(): void
    {
        // Apaga a tabela 'produtos' se existir.
        Schema::dropIfExists('produtos');
    }
};
