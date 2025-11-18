<x-layouts.app>
  <head>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

  </head>

  <body class="container">
    <h1>Comprar Produtos</h1>

        <div class="mt-16 row-right">
        <a href="{{ route('pedidos.index') }}" class="btn btn-primary btn-icon" aria-label="Ver carrinho">
            <flux:icon name="shopping-cart"/>
            Ver Carrinho
        </a>
        </div><br>

      {{-- Grid de produtos --}}
      <section>
        <div class="grid-shop">
          @forelse ($produtos as $produto)
            <article class="product-card">
              <div class="product-media">
                @if($produto->imagem)
                  <img src="{{ asset($produto->imagem) }}" alt="Imagem de {{ $produto->nome }}" loading="lazy">
                @else
                  <span class="muted">sem imagem</span>
                @endif
              </div>

              <div class="product-name">{{ $produto->nome }}</div>
              <div class="meta" style="margin-bottom:8px">
          @if($produto->categoria) Categoria: <strong>{{ $produto->categoria->nome }}</strong><br> @endif
          @if(!empty($produto->tamanho)) Tamanho: <strong>{{ $produto->tamanho }}</strong><br> @endif
          @if(isset($produto->estoque)) Estoque: <strong>{{ $produto->estoque }}</strong> @endif
        </div>

              <div class="price">
                <strong>R$ {{ number_format($produto->preco, 2, ',', '.') }}</strong>
              </div>

              {{-- Form por produto: adiciona direto ao carrinho --}}

              

              <form action="{{ route('pedidos.store') }}" method="POST" class="card-actions">
                 @csrf
                <input type="hidden" name="produto_id" value="{{ $produto->id }}">
                <input type="number" name="quantidade" value="1" min="1" class="input qty">
                <a href="{{ route('pedidos.produto.show', $produto) }}"
                  class="btn btn-primary"
                  aria-label="Ver detalhes de {{ $produto->nome }}">
                  Detalhes
                </a>
                <button type="submit" class="btn btn-success">Comprar</button>
              </form>
            </article>
          @empty
            <p class="muted">Nenhum produto disponível.</p>
          @endforelse
        </div>

        {{-- Paginação (se no controller você usar paginate()) --}}
        @if(method_exists($produtos, 'links'))
          <div class="mt-16">
            {{ $produtos->links() }}
          </div>
        @endif
      </section>
    </div>

  </body>
</x-layouts.app>
