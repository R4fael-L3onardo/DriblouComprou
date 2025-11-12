<x-layouts.app>

  <head>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  </head>

  <body class="container">
    <h1>Lista de Produtos Cadastrados</h1>

    <p class="mb-16">
      <a class="btn btn-primary" href="{{ route('produtos.create') }}">+ Novo produto</a>
    </p>

    @if ($produtos->isEmpty())
    <p class="muted">Nenhum produto cadastrado.</p>
    @else
    <ul class="list">
      @foreach ($produtos as $produto)
      <li class="item">
        #{{ $produto->id }} — {{ $produto->nome }} — Categoria: {{ $produto->categoria->nome ?? 'Sem categoria' }}
        — R$ {{ number_format($produto->preco, 2, ',', '.') }}
        @if ($produto->imagem)
          <div class="thumb">        {{-- ou .thumb-64 / .thumb-96 --}}
            <img
              src="{{ asset($produto->imagem) }}"
              alt="Imagem de {{ $produto->nome }}"
              loading="lazy">
          </div>
        @endif


        <span style="margin-left:8px;">
          <a href="{{ route('produtos.show', $produto) }}" class="btn btn-secondary">Ver</a>
          <a href="{{ route('produtos.edit', $produto) }}" class="btn btn-secondary">Editar</a>
          <form action="{{ route('produtos.destroy', $produto) }}" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <button
              type="button"
              class="btn btn-danger"
              data-action="delete"
              data-nome="{{ $produto->nome }}">
              Excluir
            </button>
          </form>


        </span>

      </li>
      @endforeach
    </ul>
    @endif

  </body>

</x-layouts.app>