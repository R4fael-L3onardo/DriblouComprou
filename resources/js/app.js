// resources/js/app.js

// Confirma que o JS carregou
console.log('app.js carregado');

// Importa SweetAlert2 (instalado via npm) e o CSS do modal
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

/**
 * Confirmação de exclusão (delegação de eventos)
 * Botões devem ter:
 *   type="button" class="btn btn-danger" data-action="delete" data-nome="Nome do item"
 *   dentro de um <form method="POST"> com @csrf e @method('DELETE')
 */
document.addEventListener('click', async (e) => {
  const btn = e.target.closest('[data-action="delete"]');
  if (!btn) return;

  const form = btn.closest('form');
  if (!form) return;

  const nome = btn.dataset.nome || 'este item';

  try {
    const result = await Swal.fire({
      title: `Excluir ${nome}?`,
      text: 'Essa ação não pode ser desfeita.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sim, excluir',
      cancelButtonText: 'Cancelar',
      reverseButtons: true,
      confirmButtonColor: '#d33',
    });

    if (result.isConfirmed) {
      form.submit();
    }
  } catch (err) {
    console.error('Falha ao abrir o SweetAlert2:', err);
    // Fallback simples
    if (confirm(`Excluir ${nome}?`)) form.submit();
  }
});
