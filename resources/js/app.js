// Mensagem simples no console para indicar que o app.js foi carregado corretamente.
console.log('app.js carregado');

// Importa a biblioteca SweetAlert2, usada para mostrar pop-ups bonitos de confirmação/alerta.
import Swal from 'sweetalert2';
// Importa o CSS padrão do SweetAlert2 para estilizar os modais.
import 'sweetalert2/dist/sweetalert2.min.css';

// Adiciona um listener global de clique no documento inteiro.
// Isso é "delegação de evento": em vez de colocar um onclick em cada botão,
// a gente escuta todos os cliques e filtra só o que interessa.
document.addEventListener('click', async (e) => {
  // Procura o elemento mais próximo do clique que tenha o atributo data-action="delete".
  // Ou seja, qualquer botão/link com data-action="delete" vai cair aqui.
  const btn = e.target.closest('[data-action="delete"]');
  if (!btn) return;  // Se não for um botão de delete, sai da função.

  // Pega o formulário mais próximo do botão (normalmente o <form> de delete).
  const form = btn.closest('form');
  if (!form) return; // Se não achar form, não faz nada.

  // Pega o nome do item que vamos mostrar na mensagem.
  // Ele vem de um data-nome="..." no HTML. Se não tiver, usa "este item" como padrão.
  const nome = btn.dataset.nome || 'este item';

  try {
    // Abre o modal de confirmação do SweetAlert2.
    // Como Swal.fire() retorna uma Promise, usamos await.
    const result = await Swal.fire({
      title: `Excluir ${nome}?`,              // título do modal
      text: 'Essa ação não pode ser desfeita.', // texto explicando
      icon: 'warning',                          // ícone de alerta
      showCancelButton: true,                   // mostra botão de cancelar
      confirmButtonText: 'Sim, excluir',        // texto do botão de confirmação
      cancelButtonText: 'Cancelar',             // texto do botão de cancelar
      reverseButtons: true,                     // inverte a ordem dos botões
      confirmButtonColor: '#d33',               // cor vermelha pro botão de confirmar
    });

    // Se o usuário confirmou (clicou em "Sim, excluir"):
    if (result.isConfirmed) {
      // Envia o formulário normalmente (faz o DELETE no back-end).
      form.submit();
    }
  } catch (err) {
    // Se der algum erro ao abrir o SweetAlert2 (por exemplo, a lib não carregou),
    // mostramos o erro no console...
    console.error('Falha ao abrir o SweetAlert2:', err);
    // ...e caímos num fallback usando o confirm() nativo do navegador.
    if (confirm(`Excluir ${nome}?`)) form.submit();
  }
});
