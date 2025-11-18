{{-- resources/views/pedidos/show.blade.php --}}
<x-layouts.app :title="'Detalhes | ' . $produto->nome">
  <head>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
      .prod-wrap{display:grid; grid-template-columns: 360px 1fr; gap:20px}
      @media (max-width:900px){ .prod-wrap{grid-template-columns:1fr} }
      .prod-media{border:1px solid var(--border); border-radius:12px; background:#fff; overflow:hidden; display:flex; align-items:center; justify-content:center; aspect-ratio: 4/3}
      .prod-media img{width:100%; height:100%; object-fit:contain}
      .meta{color:var(--muted)}
      .price{font-size:1.4rem; font-weight:800}
      .actions{display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin-top:12px}
      .qty{width:96px}
    </style>
  </head>

  <body class="container">

    <div class="card prod-wrap">
      <div class="prod-media">
        @if ($produto->imagem)
          <img src="{{ asset($produto->imagem) }}" alt="Imagem de {{ $produto->nome }}">
        @else
          <span class="muted">sem imagem</span>
        @endif
      </div>

      <div>
        <h1 style="margin:0 0 8px 0">{{ $produto->nome }}</h1>

        <div class="meta" style="margin-bottom:8px">
          @if($produto->categoria) Categoria: <strong>{{ $produto->categoria->nome }}</strong> @endif
          @if(!empty($produto->tamanho)) — Tamanho: <strong>{{ $produto->tamanho }}</strong> @endif
          @if(isset($produto->estoque)) — Estoque: <strong>{{ $produto->estoque }}</strong> @endif
        </div>

        <div class="price">R$ {{ number_format($produto->preco, 2, ',', '.') }}</div>

        {{-- Formulário: adicionar ao carrinho (POST) --}}
<form action="{{ route('pedidos.store') }}" method="POST" class="actions">
  @csrf
  <input type="hidden" name="produto_id" value="{{ $produto->id }}">
  <input type="number" name="quantidade" min="1" value="1" class="input qty" required>
  <button type="submit" class="btn btn-success">Adicionar ao Carrinho</button>

  {{-- Voltar à loja (GET) como link/botão --}}
  <a href="{{ route('pedidos.create') }}" class="btn btn-primary">Voltar à Loja</a>
</form>

      </div>
    </div>
  </body>
</x-layouts.app>
