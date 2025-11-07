<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <- importa o Auth
use App\Models\Produto; // diz quem é Produto
use Illuminate\Support\Facades\File;   // p/ criar pasta se não existir
use Illuminate\Support\Str;            // p/ nome único (opcional)

class ProdutoController extends Controller
{
    public function index()
    {
        // Obtém todos os produtos criados pelo usuário autenticado
        $produtos = Produto::where('created_by', Auth::id())->with('user')->get();

        // Retorna a view com os produtos compact é usado para passar variáveis para a view
        return view('produtos.index', compact('produtos'));
    }

    public function create()
    {
        // Só mostra o formlário de novo produto
        return view('produtos.create');
    }

    public function store(Request $request)
    {
        // 1) Validação - se falhar, volta com erros
        $dados = $request->validate([
            'nome'     => ['required', 'string', 'max:255'],
            'preco'    => ['required', 'numeric', 'min:0'],
            'tamanho'  => ['required', 'string', 'max:5'],
            'imagem'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], // até 2MB
            'estoque'  => ['required', 'integer', 'min:0']
        ]);

        // 2) Se veio arquivo, mover para public/imagem/produtos e guardar o caminho
        if ($request->hasFile('imagem')) {
            $file = $request->file('imagem');

            // garante que a pasta exista: public/imagem/produtos
            $destino = public_path('imagens/produtos');
            File::ensureDirectoryExists($destino);

            // nome de arquivo único e “seguro”
            $extensao = $file->getClientOriginalExtension();
            $nomeArquivo = Str::uuid()->toString() . '.' . $extensao;

            // move para a pasta pública
            $file->move($destino, $nomeArquivo);

            // salva caminho relativo para usar com asset()
            $dados['imagem'] = 'imagens/produtos/' . $nomeArquivo;
        }

        // 3) Atribui o dono do registro
        $dados['created_by'] = Auth::id();

        // 4) Salva no banco de dados
        $produto = Produto::create($dados);

        // 5) Redireciona para a página de detalhes
        return redirect()->route('produtos.show', $produto)
            ->with('sucesso', 'Produto criado!');
    }

    public function show(Produto $produto)
    {
        // Segurança: só o dono pode ver
        abort_if($produto->created_by !== Auth::id(), 403);

        // Envia um único produto para a view
        return view('produtos.show', compact('produto'));
    }


    public function edit(Produto $produto)
    {
        // só o dono pode editar
        abort_if($produto->created_by !== Auth::id(), 403);

        return view('produtos.edit', compact('produto'));
    }

    public function update(Request $request,Produto $produto)
    {
        // 1) Segurança: só o dono pode editar
        abort_if($produto->created_by !== Auth::id(), 403);

        // 2)Validação dos campos
        $dados = $request->validate([
            'nome'     => ['required', 'string', 'max:255'],
            'preco'    => ['required', 'numeric', 'min:0'],
            'tamanho'  => ['required', 'string', 'max:5'],
            'imagem'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], // até 2MB
            'estoque'  => ['required', 'integer', 'min:0']
        ]);

        //3) Se veio nova imagem: apaga a antiga e salva a nova
        if ($request->hasFile('imagem')){
            if ($produto->imagem && File::exists(public_path($produto->imagem))) {
                File::delete(public_path($produto->imagem)); // remove a imagem antiga
            }

            $file = $request->file('imagem');
            $destino = public_path('imagens/produtos');
            File::ensureDirectoryExists($destino);

            $extensao = $file->getClientOriginalExtension();
            $nomeArquivo = Str::uuid()->toString() . '.' . $extensao;

            $file->move($destino, $nomeArquivo);

            $dados['imagem'] = 'imagens/produtos/'.$nomeArquivo; // caminho relativo p/ asset()
        }

        // 4) Atualiza no banco
        $produto->update($dados);

        // 5) Volta para os detalhes
        return redirect()
            ->route('produtos.show', $produto)
            ->with('sucesso', 'Produto atualizado!');
    }
}
