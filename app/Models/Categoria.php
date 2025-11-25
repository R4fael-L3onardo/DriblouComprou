<?php

// Namespace: indica que essa classe está no grupo de Models da aplicação.
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Model Categoria → representa a tabela 'categorias' no banco.
class Categoria extends Model
{
    // Trait que permite usar factories (útil para testes / seeders).
    use HasFactory;

    // $fillable define quais campos podem ser preenchidos em massa (mass assignment),
    // por exemplo quando usamos Categoria::create([...]).
    protected $fillable = [
        'nome',       // nome da categoria
        'created_by', // id do usuário que criou a categoria
    ];

    // Relacionamento: UMA categoria pertence a UM usuário.
    // 'created_by' é a chave estrangeira que aponta para a tabela 'users'.
    public function user()
    {
        // belongsTo(User::class, 'created_by') → essa categoria foi criada por um usuário.
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relacionamento: UMA categoria tem MUITOS produtos.
    public function produtos()
    {
        // hasMany(Produto::class, 'categoria_id') → vários produtos estão ligados a essa categoria.
        return $this->hasMany(Produto::class, 'categoria_id');
    }

}
