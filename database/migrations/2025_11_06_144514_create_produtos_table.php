<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('produtos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');                   // texto curto (até 255) para o nome
            $table->decimal('preco', 10, 2);          // número com 2 casas (ex.: 199.90)
            $table->string('tamanho', 5);             // ex.: P, M, G, GG (curtinho)
            $table->string('imagem')->nullable();      // caminho do arquivo (ex.: storage/products/camisa1.jpg)
            $table->integer('estoque')->default(0);   // quantidade disponível; começa em 0 por padrão
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produtos');
    }
};
