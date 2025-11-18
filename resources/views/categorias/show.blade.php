{{-- resources/views/categorias/show.blade.php --}}
<x-layouts.app>

  <head>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  </head>

  <body class="container">
    <h1>Detalhes da Categoria</h1>

    @if (session('sucesso'))
      <p class="alert alert-success">{{ session('sucesso') }}</p>
    @endif

    <div class="card" style="display:grid;grid-template-columns:1fr;gap:12px;align-items:start;">

      <div>
        <h2 style="margin:0 0 8px 0;">{{ $categoria->nome }}</h2>

        <div style="margin-top:12px; display:flex; gap:8px; flex-wrap:wrap;">
          <a href="{{ route('categorias.edit', $categoria) }}" class="btn btn-primary">Editar</a>

          <form action="{{ route('categorias.destroy', $categoria) }}" method="POST" class="inline" style="display:inline;">
            @csrf
            @method('DELETE')
            <button
              type="button"
              class="btn btn-danger"
              data-action="delete"
              data-nome="{{ $categoria->nome }}">
              Excluir
            </button>
          </form>

          <a href="{{ route('categorias.index') }}" class="btn">Voltar</a>
        </div>
      </div>

      {{-- Lista de produtos dessa categoria (se houver relação produtos()) --}}
      <div class="mt-16">
        <h2>Produtos nesta categoria</h2>

        @php
          // Garante a coleção (caso não tenha sido eager loaded no controller)
          $produtosDaCategoria = isset($categoria->produtos) ? $categoria->produtos : collect();
        @endphp

        @if ($produtosDaCategoria->isEmpty())
          <p class="muted">Nenhum produto cadastrado nesta categoria.</p>
        @else
          <ul class="list">
            @foreach ($produtosDaCategoria as $produto)
              <li class="item">
                <div style="display:flex; align-items:center; gap:8px;">
                  @if ($produto->imagem)
                    <img
                      src="{{ asset($produto->imagem) }}"
                      alt="Imagem de {{ $produto->nome }}"
                      width="64" height="64"
                      class="img-thumb"
                      style="width:64px;height:64px;"
                    >
                  @endif

                  <div>
                    <div><strong>#{{ $produto->id }}</strong> — {{ $produto->nome }}</div>
                    <div class="muted">R$ {{ number_format($produto->preco, 2, ',', '.') }}</div>
                  </div>
                </div>

                <span class="gap-8" style="display:flex; align-items:center;">
                  <a href="{{ route('produtos.show', $produto) }}" class="btn btn-secondary">Ver</a>
                  <a href="{{ route('produtos.edit', $produto) }}" class="btn btn-secondary">Editar</a>
                </span>
              </li>
            @endforeach
          </ul>
        @endif
      </div>
    </div>

  </body>

</x-layouts.app>