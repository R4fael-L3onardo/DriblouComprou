<?php

// Define o namespace da classe, ou seja, em qual “pasta lógica” ela está
// dentro do projeto (app/Models). Isso ajuda o PHP e o Laravel a organizarem
// e encontrarem as classes corretamente.
namespace App\Models;

// Importa o trait HasFactory, que permite criar instâncias de Categoria usando factories
// (útil em testes e seeds, ex.: Categoria::factory()->create()).
use Illuminate\Database\Eloquent\Factories\HasFactory;

// Importa a classe base Model do Eloquent, que representa uma tabela no banco de dados.
// Toda classe que estende Model passa a ter recursos do Eloquent (consulta, insert, update, etc.).
use Illuminate\Database\Eloquent\Model;

// Declara a classe Categoria, que representa a tabela "categorias" no banco de dados.
// Ela estende Model, então é um model Eloquent.
class Categoria extends Model
{
    // Usa o trait HasFactory, permitindo usar factories com esse model.
    // Exemplo: Categoria::factory()->count(10)->create();
    use HasFactory;

    // Define quais campos podem ser preenchidos em massa (mass assignment),
    // ou seja, quando você usar Categoria::create([...]) ou $categoria->update([...]).
    // Qualquer campo que NÃO estiver nessa lista não poderá ser preenchido desse jeito.
    protected $fillable = [
        'nome',       // nome da categoria (ex.: "Camisas", "Promoções")
        'descricao',  // descrição mais detalhada da categoria
        'created_by', // ID do usuário que criou essa categoria (chave estrangeira para users.id)
    ];

    // Define o relacionamento entre Categoria e User.
    // Cada categoria PERTENCE a UM usuário (quem criou a categoria).
    public function user()
    {
        // belongsTo indica que essa Categoria está ligada a UM usuário.
        // User::class é o model que representa a tabela 'users'.
        // 'created_by' é a coluna na tabela 'categorias' que guarda o ID do usuário.
        //
        // Exemplos de uso:
        // $categoria->user        -> retorna o usuário que criou a categoria.
        // $categoria->user->name  -> nome desse usuário.
        return $this->belongsTo(User::class, 'created_by');
    }
}
