<x-layouts.app>
  <head>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>

    </style>
  </head>

  <body class="container">
    <h1>Seu Carrinho</h1>

    @if (!$pedido || $itens->isEmpty())
      <p class="muted">Carrinho vazio.</p><br>
      <div class="form-actions" style="width:100%;">
        <a href="{{ route('pedidos.create') }}" class="btn btn-primary btn-block">
          Adicionar produtos
        </a>
      </div>     
    @else
      <div class="cart-layout">
        <ul class="list">
          @foreach ($itens as $item)
            <li class="item">
              <div class="item-left">
                @if ($item->produto?->imagem)
                  <div class="thumb">
                    <img src="{{ asset($item->produto->imagem) }}" alt="Imagem de {{ $item->produto->nome }}" loading="lazy">
                  </div>
                @else
                  <div class="thumb"><span class="muted">sem imagem</span></div>
                @endif>

                <div>
                  <div><strong>{{ $item->produto?->nome ?? 'Produto removido' }}</strong></div>
                  <div class="muted">
                    R$ {{ number_format($item->preco_unitario, 2, ',', '.') }}
                    × {{ $item->quantidade }}
                    = <strong>R$ {{ number_format($item->subtotal, 2, ',', '.') }}</strong>
                  </div>
                </div>
              </div>

              <div class="item-actions">
                {{-- atualizar quantidade --}}
                <form action="{{ route('pedidos.itens.update', $item) }}" method="POST" class="center gap-8">
                  @csrf @method('PUT')
                  <input type="number" name="quantidade" min="1" value="{{ $item->quantidade }}" class="input qty-input" required>
                  <button class="btn btn-secondary" type="submit">Atualizar</button>
                </form>

                {{-- remover item --}}
                <form action="{{ route('pedidos.itens.destroy', $item) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button
                    type="button"
                    class="btn btn-danger"
                    data-action="delete"
                    data-nome="{{ $item->produto->nome ?? 'este item' }}">
                    Remover
                </button>
                </form>

              </div>
            </li>
          @endforeach
        </ul>

        <div class="card cart-summary">
          <div><strong>Total:</strong></div>
          <div style="font-size:1.25rem"><strong>R$ {{ number_format($total, 2, ',', '.') }}</strong></div>
          <form action="{{ route('pedidos.finalizar', $pedido) }}" method="POST">
              @csrf
              <button type="submit" class="btn btn-primary">
                  Finalizar Pedido
              </button>
          </form>
        </div>

        <div class="form-actions" style="width:100%;">
          <a href="{{ route('pedidos.create') }}" class="btn btn-primary btn-block">
            Voltar à Loja
          </a>
        </div>
      </div>
    @endif
  </body>
</x-layouts.app>
