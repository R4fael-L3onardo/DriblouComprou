<x-layouts.app :title="__('Dashboard')">

    <head>
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    </head>

    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">

            {{-- Card 1: atalho para Pedidos (com fundo + contador) --}}
            <a href="{{ route('pedidos.create') }}"
            aria-label="Ir para Pedidos"
            class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 group hover:border-neutral-400 dark:hover:border-neutral-500 transition-colors">
            {{-- fundo da imagem --}}
            <div class="absolute inset-0 card-bg-pedidos"></div>

            {{-- overlay de hover --}}
            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors"></div>

            {{-- rótulo + contador (pills brancos e clicáveis) --}}
            <div class="absolute inset-0 flex flex-col items-center justify-center gap-2">
                <span
                class="px-3 py-1.5 rounded-md text-sm font-medium
                        bg-white text-neutral-900 border border-neutral-200 shadow-sm
                        transition-all hover:shadow-md hover:ring-1 hover:ring-neutral-300">
                Comprar Produtos
                </span>

                <span
                class="px-2 py-0.5 rounded-md text-xs font-semibold
                        bg-white text-neutral-900 border border-neutral-200 shadow-sm
                        transition-all hover:shadow-md hover:ring-1 hover:ring-neutral-300">
                {{ $totalPedidos ?? 0 }} pedidos
                </span>
            </div>
            </a>

            {{-- Card 2: atalho para Produtos (com fundo + contador) --}}
            <a href="{{ route('produtos.index') }}"
                aria-label="Ir para Produtos"
                class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 group hover:border-neutral-400 dark:hover:border-neutral-500 transition-colors">
                {{-- fundo da imagem --}}
                <div class="absolute inset-0 card-bg-produtos"></div>

                {{-- overlay de hover --}}
                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors"></div>

                {{-- rótulo + contador (pills brancos e clicáveis) --}}
                <div class="absolute inset-0 flex flex-col items-center justify-center gap-2">
                    <span
                        class="px-3 py-1.5 rounded-md text-sm font-medium
                               bg-white text-neutral-900 border border-neutral-200 shadow-sm
                               transition-all hover:shadow-md hover:ring-1 hover:ring-neutral-300">
                        Cadastrar Produtos
                    </span>

                    <span
                        class="px-2 py-0.5 rounded-md text-xs font-semibold
                               bg-white text-neutral-900 border border-neutral-200 shadow-sm
                               transition-all hover:shadow-md hover:ring-1 hover:ring-neutral-300">
                        {{ $totalProdutos ?? 0 }} itens
                    </span>
                </div>
            </a>

            {{-- Card 3: atalho para Categorias (com fundo + contador) --}}
            <a href="{{ route('categorias.index') }}"
                aria-label="Ir para Categorias"
                class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 group hover:border-neutral-400 dark:hover:border-neutral-500 transition-colors">
                {{-- fundo da imagem --}}
                <div class="absolute inset-0 card-bg-categorias"></div>

                {{-- overlay de hover --}}
                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors"></div>

                {{-- rótulo + contador (pills brancos e clicáveis) --}}
                <div class="absolute inset-0 flex flex-col items-center justify-center gap-2">
                    <span
                        class="px-3 py-1.5 rounded-md text-sm font-medium
                            bg-white text-neutral-900 border border-neutral-200 shadow-sm
                            transition-all hover:shadow-md hover:ring-1 hover:ring-neutral-300">
                        Cadastrar Categorias
                    </span>

                    <span
                        class="px-2 py-0.5 rounded-md text-xs font-semibold
                            bg-white text-neutral-900 border border-neutral-200 shadow-sm
                            transition-all hover:shadow-md hover:ring-1 hover:ring-neutral-300">
                        {{ $totalCategorias ?? 0 }} categorias
                    </span>
                </div>
            </a>

        </div>
    </div>
</x-layouts.app>