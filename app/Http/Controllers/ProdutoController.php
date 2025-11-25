<?php

// Namespace: indica que essa classe está no grupo de controllers HTTP da aplicação.
namespace App\Http\Controllers;

// Importa classes e facades que serão usadas no controller.
use Illuminate\Http\Request;          // Representa a requisição HTTP (dados do form, arquivos, etc.).
use Illuminate\Support\Facades\Auth;  // Para pegar o usuário logado (Auth::id()).
use Illuminate\Support\Facades\File;  // Para manipular arquivos no disco (criar pasta, deletar imagem, etc.).
use Illuminate\Support\Str;           // Para gerar strings auxiliares (ex.: UUID pro nome de arquivo).
use App\Models\Produto;               // Model da tabela 'produtos'.
use App\Models\Categoria;             // Model da tabela 'categorias'.
use App\Models\Pedido;                // Model da tabela 'pedidos' (usado no dashboard).

// Controller responsável por tudo que é relacionado a PRODUTOS.
class ProdutoController extends Controller
{
    // INDEX: lista os produtos do usuário logado.
    public function index()
    {
        $produtos = Produto::where('created_by', Auth::id())   // só produtos criados pelo usuário logado
            ->with('categoria')                                // carrega a categoria de cada produto (eager loading)
            ->orderByDesc('id')                                // ordena do mais novo para o mais velho
            ->paginate(6)                                      // pagina (aqui está 1 por página, pra teste/demonstr.)
            ->withQueryString();                               // mantém query string na paginação

        // Retorna a view de listagem de produtos, passando a coleção de produtos.
        return view('produtos.index', compact('produtos'));
    }

    // CREATE: mostra o formulário para criar um novo produto.
    public function create()
    {
        // Busca todas as categorias (id e nome) para preencher o <select> no formulário.
        $categorias = Categoria::orderBy('nome')->get(['id', 'nome']);

        // Retorna a view do formulário de criação, com a lista de categorias.
        return view('produtos.create', compact('categorias'));
    }

    // STORE: recebe os dados do formulário de criação e salva um novo produto.
    public function store(Request $request)
    {
        // Valida os dados do formulário:
        // - nome obrigatório, texto, até 255 caracteres
        // - preco numérico, >= 0
        // - tamanho obrigatório, string até 5 caracteres (ex.: P, M, G, GG)
        // - imagem opcional, mas se vier deve ser uma imagem (jpg, jpeg, png, webp) até 2MB
        // - estoque inteiro, >= 0
        // - categoria_id deve existir na tabela categorias
        $dados = $request->validate([
            'nome'         => ['required', 'string', 'max:255'],
            'preco'        => ['required', 'numeric', 'min:0'],
            'tamanho'      => ['required', 'string', 'max:5'],
            'imagem'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'estoque'      => ['required', 'integer', 'min:0'],
            'categoria_id' => ['required', 'exists:categorias,id'],
        ]);

        // Se o usuário enviou uma imagem, faz o upload.
        if ($request->hasFile('imagem')) {
            $file = $request->file('imagem');                        // pega o arquivo enviado
            $destino = public_path('imagens/produtos');              // pasta de destino dentro de public/
            File::ensureDirectoryExists($destino);                   // garante que a pasta existe

            $extensao = $file->getClientOriginalExtension();         // pega a extensão original (jpg, png...)
            // Gera um nome de arquivo único usando UUID para evitar conflitos.
            $nomeArquivo = Str::uuid()->toString() . '.' . $extensao;

            // Move o arquivo para a pasta de destino com o novo nome.
            $file->move($destino, $nomeArquivo);
            // Salva no array $dados o caminho relativo da imagem, para gravar no banco.
            $dados['imagem'] = 'imagens/produtos/' . $nomeArquivo;
        }

        // Registra qual usuário criou esse produto (relaciona com o created_by).
        $dados['created_by'] = Auth::id();

        // Cria o produto no banco com os dados validados.
        $produto = Produto::create($dados);

        // Depois de salvar, redireciona para a página de detalhes do produto recém-criado,
        // com uma mensagem de sucesso.
        return redirect()
            ->route('produtos.show', $produto)
            ->with('sucesso', 'Produto criado!');
    }

    // SHOW: exibe os detalhes de um único produto.
    public function show(Produto $produto)
    {
        // Segurança: impede que um usuário veja produto de outro.
        // Se o created_by do produto for diferente do usuário logado, retorna erro 403.
        abort_if($produto->created_by !== Auth::id(), 403);

        // Carrega a categoria do produto.
        $produto->load('categoria');

        // Retorna a view de detalhes, passando o produto.
        return view('produtos.show', compact('produto'));
    }

    // EDIT: mostra o formulário de edição de um produto.
    public function edit(Produto $produto)
    {
        // De novo, segurança: só o dono do produto pode editar.
        abort_if($produto->created_by !== Auth::id(), 403);

        // Busca as categorias para preencher o select na tela de edição.
        $categorias = Categoria::orderBy('nome')->get(['id', 'nome']);

        // Retorna a view de edição, passando o produto atual e a lista de categorias.
        return view('produtos.edit', compact('produto', 'categorias'));
    }

    // UPDATE: recebe o formulário de edição e atualiza o produto no banco.
    public function update(Request $request, Produto $produto)
    {
        // Garante que o usuário logado é o dono do produto.
        abort_if($produto->created_by !== Auth::id(), 403);

        // Validação idêntica à do store, pois os campos são os mesmos.
        $dados = $request->validate([
            'nome'         => ['required', 'string', 'max:255'],
            'preco'        => ['required', 'numeric', 'min:0'],
            'tamanho'      => ['required', 'string', 'max:5'],
            'imagem'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'estoque'      => ['required', 'integer', 'min:0'],
            'categoria_id' => ['required', 'exists:categorias,id'],
        ]);

        // Se uma nova imagem foi enviada, precisa substituir a antiga.
        if ($request->hasFile('imagem')) {
            // Se já existe uma imagem antiga e o arquivo existe no disco, apaga.
            if ($produto->imagem && File::exists(public_path($produto->imagem))) {
                File::delete(public_path($produto->imagem));
            }

            // Repete o processo de upload: garante pasta, gera nome único, move arquivo.
            $file = $request->file('imagem');
            $destino = public_path('imagens/produtos');
            File::ensureDirectoryExists($destino);

            $extensao = $file->getClientOriginalExtension();
            $nomeArquivo = Str::uuid()->toString() . '.' . $extensao;

            $file->move($destino, $nomeArquivo);
            $dados['imagem'] = 'imagens/produtos/' . $nomeArquivo;
        }

        // Atualiza o produto com os dados (incluindo, se houver, o novo caminho da imagem).
        $produto->update($dados);

        // Redireciona para a página de detalhes do produto,
        // com mensagem informando que foi atualizado com sucesso.
        return redirect()
            ->route('produtos.show', $produto)
            ->with('sucesso', 'Produto atualizado!');
    }

    // DESTROY: exclui um produto.
    public function destroy(Produto $produto)
    {
        // Verifica se o produto realmente pertence ao usuário logado.
        abort_if($produto->created_by !== Auth::id(), 403);

        // Se o produto tem uma imagem e o arquivo existe no disco, apaga a imagem.
        if ($produto->imagem && File::exists(public_path($produto->imagem))) {
            File::delete(public_path($produto->imagem));
        }

        // Deleta o produto do banco de dados.
        $produto->delete();

        // Redireciona para a lista de produtos,
        // com mensagem de sucesso informando que foi excluído.
        return redirect()
            ->route('produtos.index')
            ->with('sucesso', 'Produto excluído com sucesso!');
    }

    // DASHBOARD: monta os números do painel do usuário (resumo).
    public function dashboard()
    {
        $userId = Auth::id();  // pega o ID do usuário logado

        // Busca um pedido aberto (carrinho) do usuário.
        $pedidoAberto = Pedido::where('created_by', $userId)
            ->where('status', 'aberto')
            ->first();

        // Se existir pedido aberto, soma as quantidades dos itens.
        // Se não existir, totalPedidos = 0.
        $totalPedidos = $pedidoAberto
            ? (int) $pedidoAberto->itens()->sum('quantidade')
            : 0;

        // Conta quantos produtos o usuário cadastrou.
        $totalProdutos   = Produto::where('created_by', $userId)->count();
        // Conta quantas categorias o usuário tem.
        $totalCategorias = Categoria::where('created_by', $userId)->count();

        // Retorna a view 'dashboard' passando os três números:
        // - totalPedidos: itens no carrinho
        // - totalProdutos: quantos produtos cadastrou
        // - totalCategorias: quantas categorias cadastrou
        return view('dashboard', compact('totalPedidos', 'totalProdutos', 'totalCategorias'));
    }
}
