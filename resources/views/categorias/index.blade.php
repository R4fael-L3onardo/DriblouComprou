<x-layouts.app>

  <head>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  </head>

  <body class="container">
    <h1>Lista de Categorias Cadastradas</h1>

    <div class="mt-16 row-right">
    <a href="{{ route('categorias.create') }}" class="btn btn-primary btn-icon">
        <flux:icon name="plus"/>
        Cadastrar Categoria
    </a>
    </div><br>

    @if ($categorias->isEmpty())
      <p class="muted">Nenhuma categoria cadastrada.</p>
    @else
      <ul class="list">
        @foreach ($categorias as $categoria)
          <li class="item">
            #{{ $categoria->id }} — {{ $categoria->nome }}

            <span style="margin-left:8px;">
              <a href="{{ route('categorias.show', $categoria) }}" class="btn btn-secondary">Ver</a>
              <a href="{{ route('categorias.edit', $categoria) }}" class="btn btn-secondary">Editar</a>

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
            </span>
          </li>
        @endforeach
      </ul>
    @endif
  </body>

</x-layouts.app>
