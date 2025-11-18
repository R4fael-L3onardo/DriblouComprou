<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\ItemPedido;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PedidoController extends Controller
{
    // INDEX = Carrinho do usuário
    public function index()
    {
        $pedido = \App\Models\Pedido::with(['itens.produto'])
        ->where('created_by', Auth::id())   // em vez de ->doUsuario(Auth::id())
        ->where('status', 'aberto')         // em vez de ->aberto()
        ->first();

        $itens = $pedido?->itens ?? collect();
        $total = $pedido?->total ?? 0;

        return view('pedidos.index', compact('pedido', 'itens', 'total'));
    }

    // CREATE = “Página de compra” (adicionar produtos ao carrinho)
    public function create()
    {
        $produtos = Produto::with('categoria')->orderBy('nome')->get(['id','nome','preco','imagem','categoria_id','tamanho','estoque']);
        return view('pedidos.create', compact('produtos'));
    }

    public function showProduto(Produto $produto)
    {
        $produto->load('categoria');
        return view('pedidos.show', compact('produto'));
    }

    // STORE = Adiciona item ao carrinho (cria carrinho se não existir)
    public function store(Request $request)
    {
        $dados = $request->validate([
            'produto_id' => ['required','exists:produtos,id'],
            'quantidade' => ['required','integer','min:1'],
        ]);

        $pedido = Pedido::firstOrCreate(
            ['created_by' => Auth::id(), 'status' => 'aberto'],
            ['created_by' => Auth::id(), 'status' => 'aberto']
        );

        $produto = Produto::findOrFail($dados['produto_id']);

        // Se já existe, soma quantidade; se não, cria
        $item = ItemPedido::where('pedido_id', $pedido->id)
            ->where('produto_id', $produto->id)
            ->first();

        if ($item) {
            $novaQtd = $item->quantidade + $dados['quantidade'];
            $item->update([
                'quantidade' => $novaQtd,
                'subtotal'   => $item->preco_unitario * $novaQtd,
            ]);
        } else {
            ItemPedido::create([
                'pedido_id'      => $pedido->id,
                'produto_id'     => $produto->id,
                'quantidade'     => $dados['quantidade'],
                'preco_unitario' => $produto->preco, // snapshot do preço atual
                'subtotal'       => $produto->preco * $dados['quantidade'],
            ]);
        }

        return redirect()->route('pedidos.index')->with('sucesso', 'Produto adicionado ao carrinho!');
    }

    // Atualiza quantidade de um item
    public function updateItem(Request $request, ItemPedido $item)
    {
        abort_if($item->pedido->created_by !== Auth::id() || $item->pedido->status !== 'aberto', 403);

        $dados = $request->validate([
            'quantidade' => ['required','integer','min:1'],
        ]);

        $item->update([
            'quantidade' => $dados['quantidade'],
            'subtotal'   => $item->preco_unitario * $dados['quantidade'],
        ]);

        return back()->with('sucesso', 'Quantidade atualizada.');
    }

    // Remove um item do carrinho
    public function destroyItem(ItemPedido $item)
    {
        abort_if($item->pedido->created_by !== Auth::id() || $item->pedido->status !== 'aberto', 403);

        $item->delete();

        return back()->with('sucesso', 'Item removido.');
    }

    // Finaliza o pedido (checkout simples)
    public function finalizar(Pedido $pedido)
    {
        abort_if($pedido->created_by !== Auth::id() || $pedido->status !== 'aberto', 403);

        if ($pedido->itens()->count() === 0) {
            return back()->with('erro', 'Seu carrinho está vazio.');
        }

        $pedido->update(['status' => 'finalizado']);

        return redirect()->route('pedidos.index')->with('sucesso', 'Compra finalizada!');
    }
}
