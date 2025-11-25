<?php

// Namespace: esse model faz parte do grupo de Models da aplicação.
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// Model Pedido → representa a tabela 'pedidos' no banco.
// Cada registro é um pedido/carrinho de um usuário.
class Pedido extends Model
{
    // Trait para poder usar factories (útil para testes/seeders).
    use HasFactory;

    // Campos que podem ser preenchidos em massa (mass assignment)
    // quando usamos Pedido::create([...]).
    protected $fillable = ['created_by', 'status'];
    // created_by → ID do usuário dono do pedido
    // status     → estado do pedido (ex.: 'aberto', 'finalizado')

    // Relacionamento: UM pedido pertence a UM usuário.
    public function user()
    {
        // belongsTo(User::class, 'created_by') → esse pedido foi criado por um usuário.
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relacionamento: UM pedido tem MUITOS itens (ItemPedido).
    public function itens()
    {
        // hasMany(ItemPedido::class, 'pedido_id') → vários itens ligados a esse pedido.
        return $this->hasMany(ItemPedido::class, 'pedido_id');
    }

    // Acessor (Accessors) para o atributo "total".
    // Permite acessar $pedido->total como se fosse um campo da tabela,
    // mas na verdade ele é calculado somando os subtotais dos itens.
    public function getTotalAttribute()
    {
        // Se o relacionamento 'itens' já foi carregado (eager load),
        // usa a coleção em memória pra somar (mais eficiente).
        return $this->relationLoaded('itens')
            ? $this->itens->sum('subtotal')
            // Senão, faz a soma direto no banco usando a relação (query sum).
            : $this->itens()->sum('subtotal');
    }
}
