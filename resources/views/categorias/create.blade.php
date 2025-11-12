<x-layouts.app>

    <head>
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    </head>

    <body class="container">
        <h1>Nova Categoria</h1>

        <form action="{{ route('categorias.store') }}" method="POST" enctype="multipart/form-data" class="form-grid">
            @csrf {{-- Proteção obrigatória contra CSRF em requisições POST --}}

            <label>Nome:
                <input type="text" name="nome" value="{{ old('nome') }}" required class="input">
            </label>
            <br>

           <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="{{ route('categorias.index') }}" class="btn">Cancelar</a>
        </form>
    </body>

</x-layouts.app>
