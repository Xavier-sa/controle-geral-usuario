const campoSenha = document.querySelector('#senha-login');
const botaoAlternarSenha = document.querySelector('#alternar-senha');

botaoAlternarSenha?.addEventListener('click', () => {
  const senhaVisivel = campoSenha.type === 'text';
  campoSenha.type = senhaVisivel ? 'password' : 'text';
  botaoAlternarSenha.setAttribute('aria-pressed', String(!senhaVisivel));
  botaoAlternarSenha.setAttribute('aria-label', senhaVisivel ? 'Mostrar senha' : 'Ocultar senha');
  botaoAlternarSenha.classList.toggle('senha-visivel', !senhaVisivel);
  campoSenha.focus();
});
