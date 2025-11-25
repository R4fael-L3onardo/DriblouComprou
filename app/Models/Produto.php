<?php

// Namespace: indica que esse model faz parte do grupo de Models da aplicação.
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Model Produto → representa a tabela 'produtos' no banco.
class Produto extends Model
{
    // Trait para usar factories (útil em testes / seeders).
    use HasFactory;

    // Campos que podem ser preenchidos em massa (mass assignment),
    // por exemplo quando usamos Produto::create([...]).
    protected $fillable = [
        'nome',        // nome do produto (camisa, boné, etc.)
        'preco',       // preço de venda
        'tamanho',     // tamanho (P, M, G, GG, numeração etc.)
        'imagem',      // caminho da imagem no disco (ex.: imagens/produtos/xxx.jpg)
        'estoque',     // quantidade disponível em estoque
        'created_by',  // id do usuário que cadastrou o produto
        'categoria_id' // id da categoria à qual esse produto pertence
    ];

    // $casts define conversões automáticas de tipos.
    // Aqui, 'preco' será sempre tratado como decimal com 2 casas.
    // Ex.: ao acessar $produto->preco, o Laravel já formata como decimal:2.
    protected $casts = [
        'preco' => 'decimal:2',
    ];

    // Relacionamento: UM produto pertence a UM usuário (quem criou).
    public function user()
    {
        // belongsTo(User::class, 'created_by') → esse produto foi cadastrado por um usuário.
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relacionamento: UM produto pertence a UMA categoria.
    public function categoria()
    {
        // belongsTo(Categoria::class, 'categoria_id') → esse produto está ligado a uma categoria.
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }
}
