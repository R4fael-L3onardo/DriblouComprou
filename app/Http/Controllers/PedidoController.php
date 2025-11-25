<?php

// Define o namespace, indicando que esse controller está no grupo de HTTP Controllers da aplicação.
namespace App\Http\Controllers;

// Importa os Models que esse controller usa.
use App\Models\Pedido;      // Model do pedido (carrinho / compra).
use App\Models\ItemPedido;  // Model dos itens dentro de um pedido.
use App\Models\Produto;     // Model dos produtos.
// Importa a classe Request (dados vindos do formulário / requisição HTTP).
use Illuminate\Http\Request;
// Importa o Auth, para pegar o usuário logado (Auth::id()).
use Illuminate\Support\Facades\Auth;

// Controller responsável pelas operações relacionadas a pedidos (carrinho de compras).
class PedidoController extends Controller
{

    // Método INDEX: mostra o "carrinho" atual do usuário (pedido aberto).
    public function index()
    {
        // Busca um pedido "aberto" do usuário logado,
        // já carregando (eager loading) os itens e o produto de cada item.
        $pedido = \App\Models\Pedido::with(['itens.produto'])
            ->where('created_by', Auth::id())   // pedido pertence ao usuário logado
            ->where('status', 'aberto')         // apenas pedido com status "aberto"
            ->first();                          // pega o primeiro (ou null se não existir)

        // Se existir pedido, pega os itens; se não existir, usa uma coleção vazia.
        $itens = $pedido?->itens ?? collect();
        // Se existir pedido, pega o total; se não, total = 0.
        $total = $pedido?->total ?? 0;

        // Retorna a view pedidos.index,
        // passando o pedido, os itens e o total para serem exibidos na tela.
        return view('pedidos.index', compact('pedido', 'itens', 'total'));
    }

    // Método CREATE: mostra a tela com a lista de produtos para o usuário escolher o que comprar.
    public function create()
    {
        // Busca todos os produtos com sua categoria,
        // ordenados por nome, selecionando apenas alguns campos.
        $produtos = Produto::with('categoria')
            ->orderBy('nome')
            ->get(['id','nome','preco','imagem','categoria_id','tamanho','estoque']);

        // Retorna a view pedidos.create com a lista de produtos disponível.
        return view('pedidos.create', compact('produtos'));
    }

    // Método SHOWPRODUTO: mostra detalhes de um produto (dentro do fluxo de compra).
    public function showProduto(Produto $produto)
    {
        // Carrega a categoria relacionada ao produto.
        $produto->load('categoria');

        // Retorna a view pedidos.show com os dados do produto.
        return view('pedidos.show', compact('produto'));
    }

    // Método STORE: adiciona um produto ao "carrinho" (pedido aberto).
    public function store(Request $request)
    {
        // Valida os dados da requisição:
        // - produto_id é obrigatório e deve existir na tabela produtos
        // - quantidade é obrigatória, inteira e pelo menos 1
        $dados = $request->validate([
            'produto_id' => ['required','exists:produtos,id'],
            'quantidade' => ['required','integer','min:1'],
        ]);

        // Busca ou cria um pedido "aberto" para o usuário logado.
        // Se não existir, cria um novo com created_by e status = 'aberto'.
        $pedido = Pedido::firstOrCreate(
            ['created_by' => Auth::id(), 'status' => 'aberto'], // critério de busca
            ['created_by' => Auth::id(), 'status' => 'aberto']  // dados para criar, se não existir
        );

        // Busca o produto que o usuário quer adicionar ao carrinho.
        $produto = Produto::findOrFail($dados['produto_id']);

        // Verifica se esse produto já está dentro do pedido como item.
        $item = ItemPedido::where('pedido_id', $pedido->id)
            ->where('produto_id', $produto->id)
            ->first();

        // Se o item já existe no carrinho, apenas atualiza a quantidade e o subtotal.
        if ($item) {
            // Soma a quantidade atual com a nova quantidade informada.
            $novaQtd = $item->quantidade + $dados['quantidade'];

            // Atualiza a quantidade e recalcula o subtotal (preço_unitário * quantidade).
            $item->update([
                'quantidade' => $novaQtd,
                'subtotal'   => $item->preco_unitario * $novaQtd,
            ]);
        } else {
            // Se o item ainda não existe no carrinho, cria um novo ItemPedido.
            ItemPedido::create([
                'pedido_id'      => $pedido->id,
                'produto_id'     => $produto->id,
                'quantidade'     => $dados['quantidade'],
                // salva o preço do produto no momento da compra (snapshot)
                'preco_unitario' => $produto->preco,
                // subtotal = preço do produto * quantidade
                'subtotal'       => $produto->preco * $dados['quantidade'],
            ]);
        }

        // Depois de adicionar ou atualizar o item, redireciona para a página do carrinho
        // com uma mensagem de sucesso.
        return redirect()
            ->route('pedidos.index')
            ->with('sucesso', 'Produto adicionado ao carrinho!');
    }

    // Método UPDATEITEM: altera a quantidade de um item específico dentro do carrinho.
    public function updateItem(Request $request, ItemPedido $item)
    {
        // Segurança/autorização:
        // Se o pedido NÃO pertence ao usuário logado OU não está "aberto",
        // retorna erro 403 (acesso proibido).
        abort_if(
            $item->pedido->created_by !== Auth::id() || $item->pedido->status !== 'aberto',
            403
        );

        // Valida a nova quantidade (tem que ser inteiro, >= 1).
        $dados = $request->validate([
            'quantidade' => ['required','integer','min:1'],
        ]);

        // Atualiza a quantidade do item e recalcula o subtotal.
        $item->update([
            'quantidade' => $dados['quantidade'],
            'subtotal'   => $item->preco_unitario * $dados['quantidade'],
        ]);

        // Volta para a página anterior (normalmente o carrinho)
        // com uma mensagem de sucesso.
        return back()->with('sucesso', 'Quantidade atualizada.');
    }

    // Método DESTROYITEM: remove um item do carrinho.
    public function destroyItem(ItemPedido $item)
    {
        // Mais uma vez, garante que o item pertence a um pedido do usuário logado
        // e que o pedido ainda está aberto.
        abort_if(
            $item->pedido->created_by !== Auth::id() || $item->pedido->status !== 'aberto',
            403
        );

        // Deleta o item do pedido (remove do carrinho).
        $item->delete();

        // Volta para a página anterior com mensagem de item removido.
        return back()->with('sucesso', 'Item removido.');
    }

    // Método FINALIZAR: finaliza o pedido (fecha o carrinho).
    public function finalizar(Pedido $pedido)
    {
        // Verifica se o pedido pertence ao usuário logado e se ainda está aberto.
        abort_if(
            $pedido->created_by !== Auth::id() || $pedido->status !== 'aberto',
            403
        );

        // Se o pedido não tiver itens, não deixa finalizar.
        if ($pedido->itens()->count() === 0) {
            return back()->with('erro', 'Seu carrinho está vazio.');
        }

        // Atualiza o status do pedido para "finalizado".
        $pedido->update(['status' => 'finalizado']);

        // Redireciona para a página do carrinho (que agora deve aparecer vazio ou finalizado)
        // com mensagem de compra finalizada.
        return view('pedidos.finish', compact('pedido'));
    }
}
