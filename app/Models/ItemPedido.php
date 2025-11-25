<?php

// Namespace: esse model faz parte do grupo de Models da aplicação.
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// Model ItemPedido → representa a tabela de itens dentro de um pedido (carrinho).
class ItemPedido extends Model
{
    // Trait para usar factories (para testes, seeders, etc.).
    use HasFactory;

    // Define explicitamente o nome da tabela no banco.
    // Por padrão o Laravel tentaria 'item_pedidos', então aqui você força 'itens_pedidos'.
    protected $table = 'itens_pedidos';

    // Campos que podem ser preenchidos em massa (mass assignment),
    // por exemplo quando usamos ItemPedido::create([...]).
    protected $fillable = [
        'pedido_id',      // ID do pedido ao qual esse item pertence
        'produto_id',     // ID do produto adicionado no item
        'quantidade',     // Quantidade desse produto no pedido
        'preco_unitario', // Preço do produto na hora da compra (snapshot)
        'subtotal',       // preco_unitario * quantidade
    ];

    // Relacionamento: UM item pertence a UM pedido.
    public function pedido()
    {
        // belongsTo(Pedido::class, 'pedido_id') → esse item faz parte de um pedido.
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    // Relacionamento: UM item pertence a UM produto.
    public function produto()
    {
        // belongsTo(Produto::class, 'produto_id') → esse item está ligado a um produto específico.
        return $this->belongsTo(Produto::class, 'produto_id');
    }
}
