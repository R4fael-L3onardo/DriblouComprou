<?php

// Importa a classe responsável pelas migrações do Laravel.
// Migration define a estrutura para criar/alterar tabelas no banco.
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
     * Aqui definimos como a tabela "produtos" será criada.
     */
    public function up(): void
    {
        // Cria uma nova tabela chamada 'produtos' no banco de dados.
        Schema::create('produtos', function (Blueprint $table) {
            // Cria uma coluna 'id' do tipo BIGINT auto-incremento
            // e define como chave primária da tabela.
            $table->id();

            // Cria a coluna 'categoria_id' do tipo UNSIGNED BIGINT (inteiro grande sem sinal),
            // que será usada como chave estrangeira para a tabela 'categorias'.
            // O ->nullable() indica que esse campo PODE ficar vazio (NULL),
            // ou seja, o produto não é obrigado a ter uma categoria vinculada.
            $table->unsignedBigInteger('categoria_id')->nullable();

            // Define que a coluna 'categoria_id' é uma chave estrangeira.
            // Ou seja, ela referencia a coluna 'id' da tabela 'categorias'.
            // Isso cria o vínculo de integridade referencial entre produtos e categorias.
            $table->foreign('categoria_id')
                  // Diz que essa chave estrangeira aponta para a coluna 'id'...
                  ->references('id')->on('categorias')
                  // Define o que acontece se o registro da categoria for excluído:
                  // onDelete('set null') = quando a categoria for deletada,
                  // o campo 'categoria_id' nos produtos que apontavam para ela
                  // será automaticamente definido como NULL (em vez de apagar o produto).
                  ->onDelete('set null');


            // Cria uma coluna 'nome' do tipo VARCHAR(255) (string padrão do Laravel),
            // onde será armazenado o nome do produto.
            $table->string('nome');                   

            // Cria uma coluna 'preco' do tipo DECIMAL(10, 2),
            // ou seja, um número com até 10 dígitos no total e 2 casas decimais.
            // Exemplo: 99999999.99
            $table->decimal('preco', 10, 2);          

            // Cria uma coluna 'tamanho' do tipo VARCHAR(5),
            // que pode armazenar tamanhos curtos como: 'P', 'M', 'G', 'GG'.
            $table->string('tamanho', 5);             

            // Cria uma coluna 'imagem' do tipo VARCHAR(255) (string padrão).
            // Essa coluna é opcional (nullable), ou seja, pode ser NULL.
            // Geralmente guarda o caminho do arquivo, ex.: 'storage/products/camisa1.jpg'.
            $table->string('imagem')->nullable();     

            // Cria uma coluna 'estoque' do tipo INTEGER,
            // que representa a quantidade disponível do produto em estoque.
            // O valor padrão definido é 0 (default(0)), caso não seja informado.
            $table->integer('estoque')->default(0);



            // Cria uma coluna 'created_by' do tipo UNSIGNED BIGINT.
            // Essa coluna vai armazenar o ID do usuário que cadastrou o produto
            // (chave estrangeira para a tabela 'users').
            $table->unsignedBigInteger('created_by');

            // Define a chave estrangeira para 'created_by':
            // - 'created_by' referencia a coluna 'id' na tabela 'users'.
            // - onDelete('cascade') significa que, se o usuário for deletado,
            //   todos os produtos criados por ele também serão excluídos automaticamente.
            $table->foreign('created_by')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            // Cria as colunas 'created_at' e 'updated_at' automaticamente.
            // 'created_at' armazena a data/hora de criação do registro.
            // 'updated_at' armazena a data/hora da última atualização do registro.
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
     * remove a tabela 'produtos' do banco de dados.
     */
    public function down(): void
    {
        // Remove a tabela 'produtos' caso ela exista.
        Schema::dropIfExists('produtos');
    }
};
