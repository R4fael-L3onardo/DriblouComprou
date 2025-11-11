<?php

// Define o namespace onde esse controller está localizado dentro do projeto.
// Controllers padrão do Laravel ficam em app/Http/Controllers.
namespace App\Http\Controllers;

// Importa o model Categoria, que representa a tabela 'categorias' no banco de dados.
use App\Models\Categoria;

// Importa a classe Request, que representa a requisição HTTP
// (dados enviados via formulário, query string, arquivos, etc.).
use Illuminate\Http\Request;

// Declara a classe CategoriaController, que herda da classe base Controller.
// Esse controller será responsável por todas as ações CRUD relacionadas a categorias.
class CategoriaController extends Controller
{
    // -------------------------------------------------------------------------
    // Método index: lista todas as categorias
    // -------------------------------------------------------------------------
    public function index()
    {
        // Busca todas as categorias no banco de dados,
        // ordenando pelo campo 'nome' em ordem crescente (A-Z).
        // ->get() executa a query e retorna uma Collection de Categoria.
        $categorias = Categoria::orderBy('nome')->get();

        // Retorna a view 'categorias.index' e envia a variável $categorias para a view.
        // compact('categorias') é um atalho para ['categorias' => $categorias].
        return view('categorias.index', compact('categorias'));
    }

    // -------------------------------------------------------------------------
    // Método create: mostra o formulário de criação de nova categoria
    // -------------------------------------------------------------------------
    public function create()
    {
        // Apenas retorna a view com o formulário para criar uma nova categoria.
        // Não precisa enviar dados adicionais, então só chama a view.
        return view('categorias.create');
    }

    // -------------------------------------------------------------------------
    // Método store: recebe os dados do formulário e salva uma nova categoria
    // -------------------------------------------------------------------------
    public function store(Request $request)
    {
        // Faz a validação dos dados enviados pelo formulário.
        // $request->validate() aplica as regras e:
        // - Se estiver tudo certo, retorna um array com os dados validados (em $dadosValidados).
        // - Se houver erro, volta para o formulário com mensagens de erro automaticamente.
        $dadosValidados = $request->validate([
            'nome'      => ['required', 'string', 'max:255'], // 'nome' é obrigatório, texto, até 255 caracteres.
            'descricao' => ['nullable', 'string'],            // 'descricao' é opcional, mas se vier deve ser texto.
        ]);

        $dadosValidados['created_by'] = Auth::id();

        // Cria uma nova categoria no banco de dados usando os dados validados.
        // Categoria::create() utiliza o array $dadosValidados
        // e só permite campos definidos em $fillable no model Categoria.
        Categoria::create($dadosValidados);

        // Após criar, redireciona o usuário para a lista de categorias (categorias.index)
        // e adiciona na sessão uma mensagem de sucesso com a chave 'success'.
        return redirect()
            ->route('categorias.index')
            ->with('success', 'Categoria criada com sucesso!');
    }

    // -------------------------------------------------------------------------
    // Método show: exibe os detalhes de uma categoria específica
    // -------------------------------------------------------------------------
    public function show(Categoria $categoria)
    {
        // O Laravel faz injeção de dependência e já busca a Categoria pelo ID
        // informado na rota, colocando o objeto diretamente em $categoria.
        //
        // Retorna a view 'categorias.show', passando essa categoria para exibição.
        return view('categorias.show', compact('categoria'));
    }

    // -------------------------------------------------------------------------
    // Método edit: mostra o formulário para editar uma categoria existente
    // -------------------------------------------------------------------------
    public function edit(Categoria $categoria)
    {
        // Recebe a categoria que será editada (também via injeção de dependência pela rota).
        // Retorna a view 'categorias.edit', enviando a categoria para preencher o formulário.
        return view('categorias.edit', compact('categoria'));
    }

    // -------------------------------------------------------------------------
    // Método update: recebe os dados do formulário de edição e atualiza a categoria
    // -------------------------------------------------------------------------
    public function update(Request $request, Categoria $categoria)
    {
        // Valida novamente os dados enviados no formulário de edição.
        // As regras são iguais às do método store().
        $dadosValidados = $request->validate([
            'nome'      => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
        ]);

        // Atualiza a categoria existente no banco de dados com os dados validados.
        // $categoria->update() só permite campos definidos em $fillable no model.
        $categoria->update($dadosValidados);

        // Após atualizar, redireciona de volta para a lista de categorias
        // e envia uma mensagem de sucesso para a sessão.
        return redirect()
            ->route('categorias.index')
            ->with('success', 'Categoria atualizada com sucesso!');
    }

    // -------------------------------------------------------------------------
    // Método destroy: exclui uma categoria do banco de dados
    // -------------------------------------------------------------------------
    public function destroy(Categoria $categoria)
    {
        // Exclui o registro da categoria do banco de dados.
        // Isso chama o método delete() do Eloquent.
        $categoria->delete();

        // Depois de excluir, redireciona para a lista de categorias
        // com uma mensagem de sucesso confirmando a exclusão.
        return redirect()
            ->route('categorias.index')
            ->with('success', 'Categoria excluída com sucesso!');
    }
}
