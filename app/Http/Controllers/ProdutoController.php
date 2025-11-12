<?php

// Define o namespace da classe, ou seja, em qual "pasta lógica" ela está dentro do projeto.
namespace App\Http\Controllers;

// Importa a classe Request, que representa a requisição HTTP (dados de formulário, arquivos, etc.).
use Illuminate\Http\Request;

// Importa a facade Auth, usada para acessar o usuário autenticado (Auth::id(), Auth::user(), etc.).
use Illuminate\Support\Facades\Auth; // <- importa o Auth

// Importa o model Produto, que representa a tabela 'produtos' no banco de dados.
use App\Models\Produto; // diz quem é Produto

// Importa a facade File, usada para manipular arquivos e diretórios (criar pasta, deletar arquivo, etc.).
use Illuminate\Support\Facades\File;   // p/ criar pasta se não existir

// Importa a classe Str, que tem helpers para strings (como gerar UUID).
use Illuminate\Support\Str;            // p/ nome único (opcional)

// Importa o model Categoria, que representa a tabela 'categorias' no banco de dados
// e será usado para listar, vincular ou filtrar produtos por categoria.
use App\Models\Categoria;

// Define a classe ProdutoController, responsável por lidar com as requisições relacionadas a produtos.
// Ela herda de Controller, que é a classe base dos controladores no Laravel.
class ProdutoController extends Controller
{
    // Método responsável por listar os produtos do usuário logado.
    public function index()
    {
        // Obtém todos os produtos cujo 'created_by' é igual ao ID do usuário autenticado (Auth::id()).
        // 'with("user")' faz o carregamento antecipado (eager loading) da relação 'user' definida no model Produto,
        // evitando consultas extras no banco ao acessar $produto->user na view.
        $produtos = Produto::where('created_by', Auth::id())->with('user')->get();

        // Retorna a view 'produtos.index', enviando a variável $produtos para a view.
        // compact('produtos') é um atalho para ['produtos' => $produtos].
        return view('produtos.index', compact('produtos'));
    }

    // Método que mostra o formulário para criar um novo produto.
    public function create()
    {
        // Busca todas as categorias cadastradas no banco de dados,
        // ordenando em ordem alfabética pelo campo 'nome'.
        // O select ['id', 'nome'] traz apenas essas duas colunas,
        // deixando a consulta mais leve (não carrega campos desnecessários).
        $categorias = Categoria::orderBy('nome')->get(['id', 'nome']);

        // Retorna a view 'produtos.create', enviando a variável $categorias
        // para que o formulário possa exibir, por exemplo, um <select>
        // com a lista de categorias disponíveis.
        //
        // compact('categorias') é um atalho para ['categorias' => $categorias].
        return view('produtos.create', compact('categorias'));
    }

    // Método que recebe os dados do formulário (POST) e salva um novo produto no banco.
    public function store(Request $request)
    {
        // 1) Validação dos dados recebidos do formulário.
        // O método validate() verifica os campos com base nas regras:
        // - Se tudo estiver ok, retorna um array com os dados já filtrados (em $dados).
        // - Se houver erro, redireciona de volta para o formulário com mensagens de erro automaticamente.
        $dados = $request->validate([
            'nome'     => ['required', 'string', 'max:255'],                     // nome é obrigatório, texto, até 255 caracteres
            'preco'    => ['required', 'numeric', 'min:0'],                      // preco é obrigatório, número, não pode ser negativo
            'tamanho'  => ['required', 'string', 'max:5'],                       // tamanho é obrigatório, texto curto, até 5 caracteres
            'imagem'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], // imagem é opcional, deve ser arquivo de imagem JPG/PNG/WEBP e até 2MB
            'estoque'  => ['required', 'integer', 'min:0'],                       // estoque é obrigatório, inteiro, mínimo 0
            'categoria' => ['required','string','max:255']
        ]);

        //
        $nomeCategoria = trim($dados['categoria']);
        $categoria = Categoria::firstOrCreate(
            ['nome' => $nomeCategoria],
            ['created_by' => Auth::id()]
        );

        //
        unset($dados['categoria']);
        $dados['categoria_id'] = $categoria->id;

        //
        if (property_exists(\App\Models\Produto::class, 'fillable') && in_array('created_by', (new \App\Models\Produto)->getFillable())) {
            $dados['created_by'] = Auth::id();
        }

        // 2) Se foi enviada uma imagem no campo 'imagem', trata o upload do arquivo.
        if ($request->hasFile('imagem')) {
            // Pega o arquivo enviado no campo 'imagem'.
            $file = $request->file('imagem');

            // Define o caminho físico (no servidor) da pasta onde as imagens serão salvas:
            // public/imagens/produtos
            // public_path() devolve o caminho da pasta 'public' do projeto.
            $destino = public_path('imagens/produtos');

            // Garante que a pasta destino exista. Se não existir, ela é criada.
            File::ensureDirectoryExists($destino);

            // Obtém a extensão original do arquivo (ex: jpg, png, webp).
            $extensao = $file->getClientOriginalExtension();

            // Gera um nome de arquivo único usando UUID (string aleatória).
            // Isso evita que um arquivo sobrescreva outro com o mesmo nome.
            $nomeArquivo = Str::uuid()->toString() . '.' . $extensao;

            // Move o arquivo enviado para a pasta destino, com o nome gerado.
            $file->move($destino, $nomeArquivo);

            // Salva no array $dados o caminho relativo da imagem,
            // que será armazenado no banco na coluna 'imagem'.
            // Ex.: 'imagens/produtos/arquivo123.jpg'
            $dados['imagem'] = 'imagens/produtos/' . $nomeArquivo;
        }

        // 3) Atribui no array $dados o ID do usuário autenticado,
        // definindo quem é o "dono" (criador) desse produto.
        $dados['created_by'] = Auth::id();

        // 4) Cria o registro no banco de dados usando o model Produto,
        // preenchendo com os dados validados (e, se houver, com caminho da imagem).
        // Produto::create() usa os campos definidos como $fillable no model.
        $produto = Produto::create($dados);

        // 5) Redireciona o usuário para a rota 'produtos.show' (página de detalhes do produto),
        // passando o objeto $produto. Também adiciona uma mensagem de sucesso na sessão.
        return redirect()->route('produtos.show', $produto)
            ->with('sucesso', 'Produto criado!');
    }

    // Método que exibe os detalhes de um único produto.
    // O Laravel faz injeção de dependência e já busca o Produto correspondente ao ID da rota.
    public function show(Produto $produto)
    {
        // Segurança: se o produto não pertence ao usuário logado, retorna erro 403 (proibido).
        // Compara o campo created_by do produto com o ID do usuário autenticado.
        $produto->load(['categoria', 'user']);
        abort_if($produto->created_by !== Auth::id(), 403);

        // Retorna a view 'produtos.show', passando o produto para ser mostrado na tela.
        return view('produtos.show', compact('produto'));

       

    }

    // Método que exibe o formulário de edição de um produto existente.
    public function edit(Produto $produto)
    {
        // Segurança: só o dono do produto pode editar.
        // Se o created_by do produto for diferente do usuário logado, retorna erro 403.
        abort_if($produto->created_by !== Auth::id(), 403);

        // Retorna a view 'produtos.edit', enviando o produto para preencher o formulário.
        return view('produtos.edit', compact('produto'));
    }

    // Método que recebe os dados do formulário de edição e atualiza o produto no banco.
    public function update(Request $request, Produto $produto)
    {
        // 1) Segurança: só o dono pode atualizar o produto.
        // Se não for o dono, aborta com 403 (Forbidden).
        abort_if($produto->created_by !== Auth::id(), 403);

        // 2) Validação dos dados enviados no formulário de edição.
        // Regras iguais às do store().
        $dados = $request->validate([
            'nome'     => ['required', 'string', 'max:255'],
            'preco'    => ['required', 'numeric', 'min:0'],
            'tamanho'  => ['required', 'string', 'max:5'],
            'imagem'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], // até 2MB
            'estoque'  => ['required', 'integer', 'min:0']
        ]);

        // 3) Se o usuário enviou uma nova imagem no formulário:
        if ($request->hasFile('imagem')) {
            // Se já existe imagem salva no produto E o arquivo existe no disco:
            if ($produto->imagem && File::exists(public_path($produto->imagem))) {
                // Exclui a imagem antiga para não acumular lixo no servidor.
                File::delete(public_path($produto->imagem)); // remove a imagem antiga
            }

            // Pega o novo arquivo enviado.
            $file = $request->file('imagem');

            // Define a pasta destino (mesma lógica do store): public/imagens/produtos.
            $destino = public_path('imagens/produtos');

            // Garante que a pasta exista.
            File::ensureDirectoryExists($destino);

            // Pega a extensão do novo arquivo.
            $extensao = $file->getClientOriginalExtension();

            // Gera um novo nome único usando UUID.
            $nomeArquivo = Str::uuid()->toString() . '.' . $extensao;

            // Move o novo arquivo para a pasta destino.
            $file->move($destino, $nomeArquivo);

            // Define, no array $dados, o novo caminho relativo da imagem,
            // que será salvo no banco no lugar da antiga.
            $dados['imagem'] = 'imagens/produtos/' . $nomeArquivo; // caminho relativo p/ asset()
        }

        // 4) Atualiza o registro do produto no banco de dados com os dados validados.
        $produto->update($dados);

        // 5) Redireciona de volta para a página de detalhes do produto,
        // com uma mensagem de sucesso na sessão.
        return redirect()
            ->route('produtos.show', $produto)
            ->with('sucesso', 'Produto atualizado!');
    }

    // Método responsável por excluir um produto.
    public function destroy(Produto $produto)
    {
        // 1) Segurança: só o dono pode excluir o produto.
        // Se não for o dono, retorna erro 403.
        abort_if($produto->created_by !== Auth::id(), 403);

        // 2) Se o produto tem uma imagem cadastrada e o arquivo existe no disco:
        if ($produto->imagem && File::exists(public_path($produto->imagem))) {
            // Deleta o arquivo da imagem do sistema de arquivos.
            File::delete(public_path($produto->imagem));
        }

        // 3) Exclui o registro do produto do banco de dados.
        $produto->delete();

        // 4) Redireciona para a lista de produtos com uma mensagem de sucesso.
        return redirect()
            ->route('produtos.index')
            ->with('sucesso', 'Produto excluído com sucesso!');
    }

    // Método para exibir alguma informação na dashboard relacionada a produtos.
    public function dashboard()
    {
        // Conta quantos produtos foram criados pelo usuário autenticado.
        // Faz uma consulta filtrando por created_by = Auth::id()
        // e pega apenas a quantidade de registros (count()).
        $totalProdutos = Produto::where('created_by', Auth::id())->count();

        // Retorna a view 'dashboard', enviando a variável $totalProdutos para ser exibida.
        return view('dashboard', compact('totalProdutos'));
    }
}
