<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produtos', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('categoria_id')->nullable();
            $table->foreign('categoria_id')
                  ->references('id')->on('categorias')
                  ->onDelete('set null');

            $table->string('nome');
            $table->decimal('preco', 10, 2);
            $table->string('tamanho', 5);
            $table->string('imagem')->nullable();
            $table->integer('estoque')->default(0);

            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produtos');
    }
};
