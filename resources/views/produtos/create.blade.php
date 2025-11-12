<x-layouts.app>

    <head>
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    </head>

    <body class="container">
        <h1>Novo Produto</h1>

        {{-- Formulário que envia para produtos.store (POST /produtos) --}}
        <form action="{{ route('produtos.store') }}" method="POST" enctype="multipart/form-data" class="form-grid">
            @csrf {{-- Proteção obrigatória contra CSRF em requisições POST --}}

            <label>Nome:
                <input type="text" name="nome" value="{{ old('nome') }}" required class="input">
            </label>
            <br>

            <label>Categoria
            <input type="text" name="categoria" value="{{ old('categoria') }}" required class="input" list="categorias">
            </label>
            <datalist id="categorias">
                @foreach ($categorias as $cat)
                    <option value="{{ $cat->nome }}"></option>
                @endforeach
            </datalist>
            @error('categoria')
                <div class="text-red-600 text-sm">{{ $message }}</div>
            @enderror
            <br>


            <label>Preço:
                <input type="number" step="0.01" name="preco" value="{{ old('preco') }}" required class="input">
            </label>
            <br>

            <label>Tamanho (ex.: P, M, G, GG):
                <input type="text" name="tamanho" value="{{ old('tamanho') }}" maxlength="5" required class="input">
            </label>
            <br>

            <label>Imagem:
                <input type="file" name="imagem" accept="image/*">
            </label><br>

            <label>Estoque:
                <input type="number" name="estoque" value="{{ old('estoque', 0) }}" min="0" required class="input">
            </label>
            <br>

            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="{{ route('produtos.index') }}" class="btn">Cancelar</a>
        </form>
    </body>

</x-layouts.app>
