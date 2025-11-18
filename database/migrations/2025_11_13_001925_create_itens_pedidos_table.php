<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('itens_pedidos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pedido_id');
            $table->unsignedBigInteger('produto_id');
            $table->integer('quantidade')->default(1);
            $table->decimal('preco_unitario', 10, 2); // snapshot do preço
            $table->decimal('subtotal', 10, 2);       // preco_unitario * quantidade

            $table->foreign('pedido_id')
                  ->references('id')->on('pedidos')
                  ->onDelete('cascade');

            $table->foreign('produto_id')
                  ->references('id')->on('produtos')
                  ->onDelete('restrict');

            $table->unique(['pedido_id', 'produto_id']); // evita duplicar o mesmo produto no carrinho

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('itens_pedidos');
    }
};
