<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoriaController extends Controller
{
    public function index()
    {

        //$categorias = Categoria::orderBy('nome')->get();
        //return view('categorias.index', compact('categorias'));
        // Paginação:
        $categorias = Categoria::where('created_by', Auth::id())
            ->orderBy('nome')
            ->paginate(1)                
            ->withQueryString();  
        
        
        return view('categorias.index', compact('categorias'));

    }

    public function create()
    {
        return view('categorias.create');
    }

    public function store(Request $request)
    {
        $dadosValidados = $request->validate([
            'nome'      => ['required', 'string', 'max:255'],
        ]);

        $dadosValidados['created_by'] = Auth::id();

        Categoria::create($dadosValidados);

        return redirect()
            ->route('categorias.index')
            ->with('success', 'Categoria criada com sucesso!');
    }

    public function show(Categoria $categoria)
    {
        $categoria->load(['produtos' => function ($q) {
        $q->where('created_by', Auth::id());
    }]);

    return view('categorias.show', compact('categoria'));
    }

    public function edit(Categoria $categoria)
    {
        return view('categorias.edit', compact('categoria'));
    }

    public function update(Request $request, Categoria $categoria)
    {
        $dadosValidados = $request->validate([
            'nome'      => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
        ]);

        $categoria->update($dadosValidados);

        return redirect()
            ->route('categorias.index')
            ->with('success', 'Categoria atualizada com sucesso!');
    }

    public function destroy(Categoria $categoria)
    {
        $categoria->delete();

        return redirect()
            ->route('categorias.index')
            ->with('success', 'Categoria excluída com sucesso!');
    }

}
