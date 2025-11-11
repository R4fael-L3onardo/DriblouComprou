<?php

// Define o namespace da classe, ou seja, em qual "pasta lógica" ela fica
// dentro da estrutura do projeto Laravel (app/Models).
namespace App\Models;

// Importa o trait HasFactory, que permite usar factories para esse model
// (útil em testes e seeders para criar registros falsos).
use Illuminate\Database\Eloquent\Factories\HasFactory;

// Importa a classe base Model do Eloquent, que representa uma tabela no banco.
use Illuminate\Database\Eloquent\Model;

// Define a classe Produto, que representa a tabela "produtos" no banco.
// Ela estende Model, então herda todos os recursos do Eloquent.
class Produto extends Model
{
    // Usa o trait HasFactory, permitindo criar instâncias de Produto
    // através de factories (ProdutoFactory), por exemplo: Produto::factory()->create().
    use HasFactory;

    // Define quais campos podem ser preenchidos em massa (mass assignment),
    // ou seja, quando você usa Produto::create($dados) ou $produto->update($dados).
    // Apenas os campos listados aqui serão aceitos nesses métodos.
    protected $fillable = [
        'nome',       // nome do produto
        'preco',      // preço do produto
        'tamanho',    // tamanho (ex.: P, M, G, GG)
        'imagem',     // caminho da imagem (ex.: imagens/produtos/arquivo.jpg)
        'estoque',    // quantidade de itens em estoque
        'created_by',  // ID do usuário que criou o produto (chave estrangeira)
        'categoria_id'
    ];

    // Define como o Laravel deve converter (cast) alguns campos
    // quando lê do banco ou grava no banco.
    protected $casts = [
        // Diz que o campo 'preco' deve ser tratado como decimal com 2 casas decimais.
        // Assim, ao acessar $produto->preco, ele já vem formatado com 2 casas.
        'preco' => 'decimal:2', // garante 2 casas ao ler
    ];

    // Define o relacionamento ENTRE Produto e User.
    // Cada produto PERTENCE a um usuário (quem criou).
    public function user()
    {
        // belongsTo indica que esse model Produto está ligado a UM registro da tabela users.
        // \App\Models\User::class -> model User que está no namespace App\Models.
        // 'created_by' é o nome da coluna na tabela 'produtos' que guarda o ID do usuário.
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    // Define um método de relacionamento chamado "categoria" dentro do model (por exemplo, Produto).
    // Esse método será usado para acessar a categoria à qual o produto pertence,
    // usando $produto->categoria.
    public function categoria()
    {
    // Indica que este model PERTENCE a uma categoria (relacionamento belongsTo).
    // Primeiro parâmetro: o model de destino (Categoria::class).
    // Segundo parâmetro: o nome da coluna de chave estrangeira neste model
    // que guarda o ID da categoria, no caso 'categoria_id'.
    //
    // Isso permite fazer:
    // $produto->categoria          -> retorna o objeto Categoria associado
    // $produto->categoria->nome    -> retorna o nome da categoria do produto
    return $this->belongsTo(Categoria::class, 'categoria_id');
}

}
