<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'preco',
        'tamanho',
        'imagem',
        'estoque',
        'created_by',
        'categoria_id',
    ];

    protected $casts = [
        'preco' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }
}
