<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration que cria a tabela 'itens_pedidos'.
return new class extends Migration {
    // Método up(): o que acontece quando aplicamos a migration.
    public function up(): void {
        Schema::create('itens_pedidos', function (Blueprint $table) {
            // Chave primária auto-incremento.
            $table->id();

            // ID do pedido ao qual esse item pertence (chave estrangeira para 'pedidos').
            $table->unsignedBigInteger('pedido_id');

            // ID do produto que foi adicionado ao pedido (chave estrangeira para 'produtos').
            $table->unsignedBigInteger('produto_id');

            // Quantidade desse produto no pedido. Valor padrão = 1.
            $table->integer('quantidade')->default(1);

            // Preço unitário do produto no momento da compra.
            // decimal(10,2) → até 10 dígitos, 2 casas decimais (ex.: 99999999,99).
            $table->decimal('preco_unitario', 10, 2);

            // Subtotal = preco_unitario * quantidade.
            $table->decimal('subtotal', 10, 2);

            // Foreign key: pedido_id referencia a tabela 'pedidos'.
            // onDelete('cascade') → se o pedido for apagado,
            // todos os itens ligados a ele também são apagados automaticamente.
            $table->foreign('pedido_id')
                  ->references('id')->on('pedidos')
                  ->onDelete('cascade');

            // Foreign key: produto_id referencia a tabela 'produtos'.
            // onDelete('restrict') → não permite apagar um produto
            // se ele estiver sendo usado em algum item de pedido.
            $table->foreign('produto_id')
                  ->references('id')->on('produtos')
                  ->onDelete('restrict');

            // Unique combinado: um mesmo produto só pode aparecer UMA VEZ
            // em cada pedido. Ou seja, não vai ter duas linhas com
            // mesmo (pedido_id, produto_id).
            $table->unique(['pedido_id', 'produto_id']);

            // created_at e updated_at.
            $table->timestamps();
        });
    }

    // Método down(): o que acontece se a migration for revertida (rollback).
    public function down(): void {
        // Apaga a tabela 'itens_pedidos' se ela existir.
        Schema::dropIfExists('itens_pedidos');
    }
};
