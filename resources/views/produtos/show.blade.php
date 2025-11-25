<x-layouts.app>

  <head>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  </head>

  <body class="container">
    <h1>Detalhes do Produto</h1>

    @if (session('sucesso'))
      <p class="alert success">{{ session('sucesso') }}</p>
    @endif

    <div class="card" style="display:grid;grid-template-columns:180px 1fr;gap:16px;align-items:start;">
      <div>
        @if ($produto->imagem)
          <img
            src="{{ asset($produto->imagem) }}"
            alt="Imagem de {{ $produto->nome }}"
            width="180" height="180"
            style="object-fit:cover;border-radius:12px;">
        @else
          <div class="muted" style="width:180px;height:180px;border:1px dashed #ccc;border-radius:12px;display:grid;place-items:center;">
            sem imagem
          </div>
        @endif
      </div>

      <div>
        <h2 style="margin:0 0 8px 0;">
          {{ optional($produto->categoria)->nome ? $produto->categoria->nome . ' - ' : '' }}{{ $produto->nome }}
        </h2>

        <p><strong>Categoria:</strong> {{ $produto->categoria->nome ?? 'Sem categoria' }}</p>
        <p><strong>Preço:</strong> R$ {{ number_format($produto->preco, 2, ',', '.') }}</p>
        <p><strong>Tamanho:</strong> {{ $produto->tamanho }}</p>
        <p><strong>Estoque:</strong> {{ $produto->estoque }}</p>

        {{-- NOVO: categoria do produto --}}
        <p><strong>Categoria:</strong> {{ optional($produto->categoria)->nome ?? '—' }}</p>

        <p class="muted" style="margin-top:8px;">
          <strong>Cadastrado por:</strong> {{ optional($produto->user)->name ?? '—' }}
        </p>

        <div style="margin-top:12px; display:flex; gap:8px; flex-wrap:wrap;">
          <a href="{{ route('produtos.edit', $produto) }}" class="btn btn-primary">Editar</a>

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

          <a href="{{ route('produtos.index') }}" class="btn">Voltar</a>
        </div>
      </div>
    </div>
  </body>

</x-layouts.app>