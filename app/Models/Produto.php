<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    use HasFactory;

    // Quais campos podem ser preenchidos em massa (ex.: Produto::create([...]))
    protected $fillable = [
        'nome',
        'preco',
        'tamanho',
        'imagem',
        'estoque',
        'created_by'
    ];

    // Como o Laravel converte tipos quando lê/grava
    protected $casts = [
        'preco' => 'decimal:2', // garante 2 casas ao ler
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
}
