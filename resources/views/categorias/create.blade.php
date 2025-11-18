<x-layouts.app>

    <head>
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    </head>

    <body class="container">
        <h1>Nova Categoria</h1>

        {{-- Formulário que envia para categorias.store (POST /categorias) --}}
        <form action="{{ route('categorias.store') }}" method="POST" class="form-grid">
            @csrf {{-- Proteção obrigatória contra CSRF --}}

            {{-- Nome da categoria (obrigatório) --}}
            <label>Nome:
                <input
                    type="text"
                    name="nome"
                    value="{{ old('nome') }}"
                    required
                    class="input"
                    placeholder="Ex.: Camisas, Chuteiras, Acessórios"
                >
            </label>
            @error('nome')
                <div class="text-red-600 text-sm">{{ $message }}</div>
            @enderror
            <br>

            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="{{ route('categorias.index') }}" class="btn">Cancelar</a>
        </form>
    </body>

</x-layouts.app>
