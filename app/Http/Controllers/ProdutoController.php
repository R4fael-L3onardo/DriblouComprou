<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Models\Produto;
use App\Models\Categoria;
use App\Models\Pedido;

class ProdutoController extends Controller
{
    public function index()
    {
        // Lista só os produtos do usuário logado, já trazendo a categoria
        //$produtos = Produto::where('created_by', Auth::id())
         //   ->with(['user', 'categoria'])
         //   ->get();

          $produtos = Produto::where('created_by', Auth::id())
            ->with('categoria')
            ->orderByDesc('id')
            ->paginate(1)                
            ->withQueryString(); 

        return view('produtos.index', compact('produtos'));
    }

    public function create()
    {
        // Envia as categorias para o <select name="categoria_id">
        $categorias = Categoria::orderBy('nome')->get(['id', 'nome']);
        return view('produtos.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        // Validação dos campos + categoria por ID existente
        $dados = $request->validate([
            'nome'         => ['required', 'string', 'max:255'],
            'preco'        => ['required', 'numeric', 'min:0'],
            'tamanho'      => ['required', 'string', 'max:5'],
            'imagem'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'estoque'      => ['required', 'integer', 'min:0'],
            'categoria_id' => ['required', 'exists:categorias,id'],
        ]);

        // Upload da imagem (opcional)
        if ($request->hasFile('imagem')) {
            $file = $request->file('imagem');
            $destino = public_path('imagens/produtos');
            File::ensureDirectoryExists($destino);

            $extensao = $file->getClientOriginalExtension();
            $nomeArquivo = Str::uuid()->toString() . '.' . $extensao;

            $file->move($destino, $nomeArquivo);
            $dados['imagem'] = 'imagens/produtos/' . $nomeArquivo; // caminho relativo a /public
        }

        // vincula o produto ao usuário
        $dados['created_by'] = Auth::id();

        // cria o produto
        $produto = Produto::create($dados);

        return redirect()
            ->route('produtos.show', $produto)
            ->with('sucesso', 'Produto criado!');
    }

    public function show(Produto $produto)
    {
        // Segurança: só o dono pode ver
        abort_if($produto->created_by !== Auth::id(), 403);

        // Carrega a categoria para exibir no show
        $produto->load('categoria');

        return view('produtos.show', compact('produto'));

       

    }

    public function edit(Produto $produto)
    {
        // Segurança: só o dono pode editar
        abort_if($produto->created_by !== Auth::id(), 403);

        // Envia categorias para o select pré-selecionado
        $categorias = Categoria::orderBy('nome')->get(['id', 'nome']);

        return view('produtos.edit', compact('produto', 'categorias'));
    }

    public function update(Request $request, Produto $produto)
    {
        // Segurança: só o dono pode atualizar
        abort_if($produto->created_by !== Auth::id(), 403);

        // Validação dos campos + categoria por ID existente
        $dados = $request->validate([
            'nome'         => ['required', 'string', 'max:255'],
            'preco'        => ['required', 'numeric', 'min:0'],
            'tamanho'      => ['required', 'string', 'max:5'],
            'imagem'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'estoque'      => ['required', 'integer', 'min:0'],
            'categoria_id' => ['required', 'exists:categorias,id'],
        ]);

        // Se enviou nova imagem: apaga a antiga (se existir) e salva a nova
        if ($request->hasFile('imagem')) {
            if ($produto->imagem && File::exists(public_path($produto->imagem))) {
                File::delete(public_path($produto->imagem));
            }

            $file = $request->file('imagem');
            $destino = public_path('imagens/produtos');
            File::ensureDirectoryExists($destino);

            $extensao = $file->getClientOriginalExtension();
            $nomeArquivo = Str::uuid()->toString() . '.' . $extensao;

            $file->move($destino, $nomeArquivo);
            $dados['imagem'] = 'imagens/produtos/' . $nomeArquivo;
        }

        $produto->update($dados);

        return redirect()
            ->route('produtos.show', $produto)
            ->with('sucesso', 'Produto atualizado!');
    }

    public function destroy(Produto $produto)
    {
        // Segurança: só o dono pode excluir
        abort_if($produto->created_by !== Auth::id(), 403);

        // Remove a imagem do disco, se existir
        if ($produto->imagem && File::exists(public_path($produto->imagem))) {
            File::delete(public_path($produto->imagem));
        }

        $produto->delete();

        return redirect()
            ->route('produtos.index')
            ->with('sucesso', 'Produto excluído com sucesso!');
    }

    public function dashboard()
    {
        $userId = Auth::id();

        // Pedido aberto do usuário
        $pedidoAberto = Pedido::where('created_by', $userId)
            ->where('status', 'aberto')
            ->first();

        // Total de itens no carrinho (soma das quantidades)
        $totalPedidos = $pedidoAberto
            ? (int) $pedidoAberto->itens()->sum('quantidade')
            : 0;

        // Outros contadores
        $totalProdutos   = Produto::where('created_by', $userId)->count();
        $totalCategorias = Categoria::where('created_by', $userId)->count(); // se categorias têm autoria

        return view('dashboard', compact('totalPedidos', 'totalProdutos', 'totalCategorias'));
    }
}
