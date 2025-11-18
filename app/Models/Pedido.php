<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = ['created_by','status'];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function itens()
    {
        return $this->hasMany(ItemPedido::class, 'pedido_id');
    }

    // Atributo: total = soma dos subtotais
    public function getTotalAttribute()
    {
        return $this->relationLoaded('itens')
            ? $this->itens->sum('subtotal')
            : $this->itens()->sum('subtotal');
    }
}