<x-layouts.app>

  <head>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  </head>

  <body class="container">
  <h1>Editar Produto</h1>

  <form action="{{ route('produtos.update', $produto) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <label>Nome:
      <input type="text" name="nome" value="{{ old('nome', $produto->nome) }}" required class="input">
    </label><br>

    <label>Preço:
      <input type="number" step="0.01" name="preco" value="{{ old('preco', $produto->preco) }}" required class="input">
    </label><br>

    <label>Tamanho:
      <input type="text" name="tamanho" value="{{ old('tamanho', $produto->tamanho) }}" maxlength="5" required class="input">
    </label><br>

    <label>Estoque:
      <input type="number" name="estoque" value="{{ old('estoque', $produto->estoque) }}" min="0" required class="input">
    </label><br>

    <p>Imagem atual:</p>
    @if ($produto->imagem)
      <img src="{{ asset($produto->imagem) }}" alt="" width="96" height="96" style="object-fit:cover;border-radius:8px;">
    @else
      <em>sem imagem</em>
    @endif
    <br>

    <label>Nova imagem (opcional):
      <input type="file" name="imagem" accept="image/*">
    </label><br><br>

    <button type="submit" class="btn btn-primary">Salvar</button>
    <a href="{{ route('produtos.index') }}" class="btn">Cancelar</a>
  </form>
  </body>
</x-layouts.app>
