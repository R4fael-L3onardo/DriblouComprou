{{-- resources/views/produtos/show.blade.php --}}
<x-layouts.app>

  <head>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  </head>

  <body class="container">
    <h1>Detalhes da Categoria</h1>

    @if (session('sucesso'))
      <p class="alert success">{{ session('sucesso') }}</p>
    @endif

    <div class="card" style="display:grid;grid-template-columns:180px 1fr;gap:16px;align-items:start;">
      
      <div>
        <h2 style="margin:0 0 8px 0;">{{ $categoria->nome }}</h2>

     

        <div style="margin-top:12px; display:flex; gap:8px; flex-wrap:wrap;">
          <a href="{{ route('categorias.edit', $categoria) }}" class="btn btn-primary">Editar</a>

          <form action="{{ route('categorias.destroy', $categoria) }}" method="POST" class="inline">
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
    </div>
  </body>

</x-layouts.app>
