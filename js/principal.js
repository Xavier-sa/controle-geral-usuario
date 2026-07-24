const barraLateral = document.querySelector('#barra-lateral');
const estruturaAplicacao = document.querySelector('#estrutura-aplicacao');
const botaoMenu = document.querySelector('#botao-menu');
const botaoRecolher = document.querySelector('#botao-recolher');
const consultaTelaPequena = window.matchMedia('(max-width: 900px)');

function atualizarEstadoMenu() {
  const telaPequena = consultaTelaPequena.matches;
  const aberto = telaPequena
    ? barraLateral.classList.contains('open')
    : !estruturaAplicacao.classList.contains('menu-recolhido');

  botaoMenu?.setAttribute('aria-expanded', String(aberto));
  botaoRecolher?.setAttribute('aria-expanded', String(aberto));
}

function abrirMenu() {
  if (consultaTelaPequena.matches) barraLateral.classList.add('open');
  else estruturaAplicacao.classList.remove('menu-recolhido');
  localStorage.setItem('prospera_menu_recolhido', 'nao');
  atualizarEstadoMenu();
}

function recolherMenu() {
  if (consultaTelaPequena.matches) barraLateral.classList.remove('open');
  else estruturaAplicacao.classList.add('menu-recolhido');
  localStorage.setItem('prospera_menu_recolhido', 'sim');
  atualizarEstadoMenu();
}

if (!consultaTelaPequena.matches && localStorage.getItem('prospera_menu_recolhido') === 'sim') {
  estruturaAplicacao.classList.add('menu-recolhido');
}

botaoMenu?.addEventListener('click', abrirMenu);
botaoRecolher?.addEventListener('click', recolherMenu);
consultaTelaPequena.addEventListener('change', () => {
  barraLateral.classList.remove('open');
  atualizarEstadoMenu();
});
atualizarEstadoMenu();

const modalTermos = document.querySelector('#modal-termos');
const confirmacaoTermos = document.querySelector('#confirmacao-termos');
const aceitarTermos = document.querySelector('#aceitar-termos');

if (!localStorage.getItem('prospera_termos_versao')) modalTermos.hidden = false;
confirmacaoTermos?.addEventListener('change', () => aceitarTermos.disabled = !confirmacaoTermos.checked);
aceitarTermos?.addEventListener('click', () => {
  localStorage.setItem('prospera_termos_versao', '1.0');
  modalTermos.hidden = true;
});

document.querySelector('#copiar-indicacao')?.addEventListener('click', async () => {
  await navigator.clipboard.writeText(document.querySelector('#link-indicacao').value);
  const aviso = document.querySelector('#aviso');
  aviso.textContent = 'Link copiado'; aviso.hidden = false;
  window.setTimeout(() => aviso.hidden = true, 2200);
});

const seletorDespesa = document.querySelector('#seletor-despesa');
const seletorCategoria = document.querySelector('#seletor-categoria');
const campoOutraDespesa = document.querySelector('#campo-outra-despesa');
const nomeOutraDespesa = document.querySelector('#nome-outra-despesa');

function atualizarOutraDespesa() {
  if (!seletorDespesa || !campoOutraDespesa || !nomeOutraDespesa) return;

  const outraDespesa = seletorDespesa.value === 'outro';
  campoOutraDespesa.hidden = !outraDespesa;
  nomeOutraDespesa.required = outraDespesa;
  if (!outraDespesa) nomeOutraDespesa.value = '';
}

seletorDespesa?.addEventListener('change', () => {
  atualizarOutraDespesa();
  if (seletorDespesa.value === 'Jogos e apostas' && seletorCategoria) {
    seletorCategoria.value = 'jogos e apostas';
  }
  if (seletorDespesa.value === 'outro') nomeOutraDespesa?.focus();
});
atualizarOutraDespesa();

const camposCusto = [...document.querySelectorAll('[data-campo-custo]')];
const situacoesCusto = [...document.querySelectorAll('[data-situacao-custo]')];
const barraPreenchimento = document.querySelector('#barra-preenchimento');
const percentualProgresso = document.querySelector('#percentual-progresso');
const textoProgresso = document.querySelector('#texto-progresso');

function atualizarProgressoCustos() {
  if (!barraPreenchimento || camposCusto.length === 0) return;

  const preenchidos = situacoesCusto.filter((situacao, indice) => situacao.value === 'sem_gasto' || (situacao.value === 'com_gasto' && converterValor(camposCusto[indice].value) > 0)).length;
  const percentual = Math.round(preenchidos / situacoesCusto.length * 100);
  const classe = percentual < 40 ? 'inicio' : percentual < 100 ? 'andamento' : 'completo';

  barraPreenchimento.classList.remove('inicio', 'andamento', 'completo');
  barraPreenchimento.classList.add(classe);
  barraPreenchimento.setAttribute('aria-valuenow', String(percentual));
  barraPreenchimento.querySelector('span').style.width = `${percentual}%`;
  percentualProgresso.textContent = `${percentual}%`;
  textoProgresso.textContent = `${preenchidos} de ${camposCusto.length} campos preenchidos`;
}

function converterValor(valor) {
  const texto = String(valor).trim();
  return Number(texto.includes(',') ? texto.replaceAll('.', '').replace(',', '.') : texto) || 0;
}

camposCusto.forEach((campo, indice) => campo.addEventListener('input', () => {
  if (converterValor(campo.value) > 0 && situacoesCusto[indice].value === 'pendente') {
    situacoesCusto[indice].value = 'com_gasto';
  }
  atualizarProgressoCustos();
}));
situacoesCusto.forEach((situacao, indice) => situacao.addEventListener('change', () => {
  const semGasto = situacao.value === 'sem_gasto';
  camposCusto[indice].disabled = semGasto;
  if (semGasto) camposCusto[indice].value = '0.00';
  atualizarProgressoCustos();
}));

const usuariosOnline = document.querySelector('#usuarios-online');
const quantidadeOnline = document.querySelector('#quantidade-online');
const rotuloOnline = document.querySelector('#rotulo-online');

async function atualizarUsuariosOnline() {
  if (!usuariosOnline || document.hidden) return;

  try {
    const resposta = await fetch('?recurso=presenca', {headers: {'Accept': 'application/json'}});
    if (!resposta.ok) return;

    const dados = await resposta.json();
    const quantidade = Number(dados.quantidade_online) || 1;
    const idiomaIngles = document.documentElement.lang === 'en';

    quantidadeOnline.textContent = String(quantidade);
    rotuloOnline.textContent = quantidade > 1
      ? (idiomaIngles ? 'users online' : 'usuários online')
      : (idiomaIngles ? 'user online' : 'usuário online');
    usuariosOnline.classList.toggle('multiplos', quantidade > 1);
  } catch (erro) {
    console.warn('Não foi possível atualizar os usuários online.', erro);
  }
}

window.setInterval(atualizarUsuariosOnline, 25000);
document.addEventListener('visibilitychange', atualizarUsuariosOnline);
