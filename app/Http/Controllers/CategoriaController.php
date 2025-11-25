<?php

// Define o namespace da classe, ou seja, em qual “pacote” lógico ela está.
namespace App\Http\Controllers;

// Importa o Model Categoria (tabela categorias no banco).
use App\Models\Categoria;
// Importa a classe Request (dados vindos do formulário / requisição HTTP).
use Illuminate\Http\Request;
// Importa o Auth, para pegar o usuário logado (Auth::id()).
use Illuminate\Support\Facades\Auth;

// Define a classe CategoriaController, responsável por controlar as categorias.
// Ela herda de Controller (classe base do Laravel).
class CategoriaController extends Controller
{
    // Método INDEX: lista as categorias do usuário logado.
    public function index()
    {
        // Monta a query nas categorias:
        // 1) Filtra só categorias criadas pelo usuário logado (created_by = Auth::id()).
        $categorias = Categoria::where('created_by', Auth::id())
            // 2) Ordena em ordem alfabética pelo nome.
            ->orderBy('nome')
            // 3) Pagina o resultado (aqui está 1 por página, só para exemplo/teste).
            ->paginate(6)
            // 4) Mantém os parâmetros da URL (ex.: ?page=2) ao navegar na paginação.
            ->withQueryString();  
        
        // Retorna a view categorias.index, passando a variável $categorias para a view.
        return view('categorias.index', compact('categorias'));
    }

    // Método CREATE: mostra o formulário para criar uma nova categoria.
    public function create()
    {
        // Apenas retorna a view com o formulário de criação.
        return view('categorias.create');
    }

    // Método STORE: recebe o formulário de criação e salva uma nova categoria no banco.
    public function store(Request $request)
    {
        // Valida os dados vindos do formulário.
        // Regra: 'nome' é obrigatório, deve ser string e no máximo 255 caracteres.
        $dadosValidados = $request->validate([
            'nome'      => ['required', 'string', 'max:255'],
        ]);

        // Adiciona o campo created_by aos dados validados,
        // para registrar qual usuário criou essa categoria.
        $dadosValidados['created_by'] = Auth::id();

        // Cria a categoria no banco usando os dados validados.
        // (equivalente a Categoria::create([...]))
        Categoria::create($dadosValidados);

        // Depois de salvar, redireciona de volta para a lista de categorias
        // e envia uma mensagem de sucesso para a sessão.
        return redirect()
            ->route('categorias.index')
            ->with('success', 'Categoria criada com sucesso!');
    }

    // Método SHOW: mostra os detalhes de uma categoria específica.
    // O Laravel injeta automaticamente o Model Categoria ($categoria) com base no ID da rota.
    public function show(Categoria $categoria)
    {
        // Carrega o relacionamento 'produtos' dessa categoria,
        // mas filtrando só produtos criados pelo usuário logado.
        $categoria->load(['produtos' => function ($q) {
            $q->where('created_by', Auth::id());
        }]);

        // Retorna a view categorias.show, passando a categoria (com seus produtos filtrados).
        return view('categorias.show', compact('categoria'));
    }

    // Método EDIT: mostra o formulário para editar uma categoria existente.
    public function edit(Categoria $categoria)
    {
        // Retorna a view de edição, passando a categoria para preencher o formulário.
        return view('categorias.edit', compact('categoria'));
    }

    // Método UPDATE: recebe o formulário de edição e atualiza a categoria no banco.
    public function update(Request $request, Categoria $categoria)
    {
        // Valida os dados do formulário de edição:
        // - nome: obrigatório, string, até 255 caracteres.
        // - descricao: opcional (nullable), mas se vier tem que ser string.
        $dadosValidados = $request->validate([
            'nome'      => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
        ]);

        // Atualiza a categoria no banco com os dados validados.
        $categoria->update($dadosValidados);

        // Redireciona de volta para a lista de categorias
        // com mensagem de sucesso.
        return redirect()
            ->route('categorias.index')
            ->with('success', 'Categoria atualizada com sucesso!');
    }

    // Método DESTROY: exclui uma categoria do banco.
    public function destroy(Categoria $categoria)
    {
        // Deleta o registro da categoria do banco de dados.
        $categoria->delete();

        // Redireciona de volta para a lista de categorias com mensagem de sucesso.
        return redirect()
            ->route('categorias.index')
            ->with('success', 'Categoria excluída com sucesso!');
    }

}
