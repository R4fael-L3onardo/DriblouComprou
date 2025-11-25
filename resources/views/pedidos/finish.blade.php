{{-- resources/views/pedidos/finish.blade.php --}}

<x-layouts.app>
  <head>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  </head>

  <body class="container">
    <main style="max-width: 640px; margin: 40px auto;">
      <section
        style="
          background: var(--panel, #fff);
          border-radius: 12px;
          padding: 24px 24px 20px;
          box-shadow: 0 8px 24px rgba(0,0,0,0.06);
        "
      >
        <header style="text-align:center; margin-bottom: 20px;">
          <div style="font-size: 40px; margin-bottom: 8px;">✅</div>
          <h1 style="margin-bottom: 4px;">Pedido finalizado com sucesso!</h1>
          <p class="muted" style="margin: 0;">
            Obrigado pela compra 😊<br>
            Seu pedido <strong>#{{ $pedido->id }}</strong> foi registrado e será processado em breve.
          </p>
        </header>

        @if ($pedido->relationLoaded('itens') || $pedido->itens()->exists())
          @php
            $itens = $pedido->itens()->with('produto')->get();
          @endphp

          <section style="margin-top: 24px;">
            <h2 style="font-size: 1.1rem; margin-bottom: 8px;">Resumo do pedido</h2>

            <ul class="list" style="margin-bottom: 12px;">
              @foreach ($itens as $item)
                <li class="item" style="display:flex; justify-content:space-between; gap:8px; align-items:center;">
                  <div>
                    <strong>{{ $item->produto->nome ?? 'Produto removido' }}</strong><br>
                    <span class="muted">
                      {{ $item->quantidade }} un —
                      R$ {{ number_format($item->preco_unitario, 2, ',', '.') }} / un
                    </span>
                  </div>
                  <div style="font-weight: 600;">
                    R$ {{ number_format($item->subtotal, 2, ',', '.') }}
                  </div>
                </li>
              @endforeach
            </ul>

            <div style="display:flex; justify-content:space-between; align-items:center; margin-top: 4px;">
              <span style="font-weight: 600;">Total do pedido</span>
              <span style="font-weight: 700; font-size: 1.1rem;">
                R$ {{ number_format($pedido->total, 2, ',', '.') }}
              </span>
            </div>
          </section>
        @endif

        <footer style="margin-top: 28px; display:flex; gap:12px; justify-content:flex-end; flex-wrap:wrap;">
          <a href="{{ route('dashboard') }}" class="btn btn-secondary">
            Ir para o Dashboard
          </a>

          <a href="{{ route('pedidos.create') }}" class="btn btn-primary">
            Voltar à Loja
          </a>
        </footer>
      </section>
    </main>
  </body>
</x-layouts.app>
