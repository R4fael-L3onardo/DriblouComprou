<?php

// Importa a classe base de Migration e utilitários para definir a estrutura da tabela.
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Retorna uma classe anônima que estende Migration.
// O Laravel vai rodar os métodos up() e down() dessa classe.
return new class extends Migration {
    // Método up(): define o que acontece quando a migration é aplicada.
    public function up(): void {
        // Cria a tabela 'pedidos' no banco.
        Schema::create('pedidos', function (Blueprint $table) {
            // Cria a coluna 'id' como chave primária auto-incremento (BIGINT).
            $table->id();

            // Coluna 'status' do tipo string.
            // default('aberto') → se não for informado, o valor padrão é 'aberto'.
            // Ex.: pedido aberto, finalizado etc.
            $table->string('status')->default('aberto');

            // Coluna 'created_by' para guardar o ID do usuário que criou o pedido.
            // unsignedBigInteger → inteiro positivo, compatível com id de users.
            $table->unsignedBigInteger('created_by');

            // Define a chave estrangeira 'created_by' apontando para a tabela 'users'.
            $table->foreign('created_by')
                  ->references('id')->on('users')
                  // onDelete('cascade') → se o usuário for apagado,
                  // os pedidos dele também serão automaticamente apagados.
                  ->onDelete('cascade');

            // Cria as colunas 'created_at' e 'updated_at'.
            $table->timestamps();
        });
    }

    // Método down(): define o que acontece quando a migration é revertida (rollback).
    public function down(): void {
        // Apaga a tabela 'pedidos' caso ela exista.
        Schema::dropIfExists('pedidos');
    }
};
