<!doctype html>
<html lang="<?= Visao::escapar($idioma) ?>">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Prospera — clareza financeira para construir um futuro abundante.">
  <meta name="theme-color" content="#10291f"><title>Prospera | Painel financeiro</title>
  <link rel="icon" href="assets/images/favicon-wx.svg" type="image/svg+xml">
  <link rel="stylesheet" href="css/style.css?v=6">
</head>
<body>
  <a class="skip-link" href="#conteudo">Pular para o conteúdo</a>
  <div class="app-shell" id="estrutura-aplicacao">
    <aside class="sidebar" id="barra-lateral">
      <div class="cabecalho-lateral">
        <div class="brand"><span class="brand-mark">↑</span><span>Prospera</span></div>
        <button class="botao-recolher" id="botao-recolher" type="button" aria-label="Recolher menu" aria-expanded="true">‹</button>
      </div>
      <nav class="nav" aria-label="Principal">
        <?php $itensMenu=['inicio'=>['visao_geral','⌂'],'rendas'=>['minhas_rendas','＋'],'custos'=>['custos_fixos','◇'],'calendario'=>['calendario_anual','▦'],'usuarios'=>['comunidade','◎']];foreach ($itensMenu as $destino => [$rotulo, $icone]): ?>
          <a class="<?= $pagina === $destino ? 'active' : '' ?>" href="?pagina=<?= $destino ?>&amp;idioma=<?= Visao::escapar($idioma) ?>"><span class="icone-menu icone-menu-<?= Visao::escapar($destino) ?>" aria-hidden="true"><?= $icone ?></span><span><?= Visao::escapar($tradutor->obter($rotulo)) ?></span></a>
        <?php endforeach; ?>
      </nav>
      <div class="sidebar-note"><?= Visao::escapar($tradutor->obter('clareza')) ?></div>
    </aside>
    <div class="main-area">
      <header class="navbar">
        <button class="icon-btn menu-btn" id="botao-menu" type="button" aria-label="Abrir menu" aria-expanded="false">☰</button><div></div>
        <div class="nav-actions">
          <div class="usuarios-online <?= $quantidadeOnline > 1 ? 'multiplos' : '' ?>" id="usuarios-online" title="Usuários online" aria-live="polite">
            <span class="indicador-online" aria-hidden="true"></span>
            <svg class="icone-online icone-usuario" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <svg class="icone-online icone-grupo" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            <strong id="quantidade-online"><?= (int) $quantidadeOnline ?></strong><span id="rotulo-online"><?= Visao::escapar($tradutor->obter($quantidadeOnline > 1 ? 'usuarios_online' : 'usuario_online')) ?></span>
          </div>
          <a class="language" href="?pagina=<?= $pagina ?>&amp;idioma=<?= $idioma === 'pt-BR' ? 'en' : 'pt-BR' ?>"><?= $idioma === 'pt-BR' ? 'EN' : 'PT' ?></a>
          <?php $nomeExibicao=$usuarioAtual->obter('apelido')?:$usuarioAtual->obter('nome'); ?><div class="user-chip"><div class="user-avatar"><?= strtoupper(substr((string)$nomeExibicao,0,2)) ?></div><span><?= Visao::escapar($nomeExibicao) ?></span></div>
          <form method="post" class="formulario-sair"><input type="hidden" name="token" value="<?= Visao::escapar($token) ?>"><input type="hidden" name="acao" value="sair"><button class="icon-btn" title="Sair" aria-label="Sair">↪</button></form>
        </div>
      </header>
      <main class="content" id="conteudo"><?= $conteudo ?></main>
      <footer class="footer"><span>© 2026 Prospera</span><span><?= Visao::escapar($tradutor->obter('suporte')) ?>: <a href="mailto:xavier@wasxtech.com.br">xavier@wasxtech.com.br</a></span></footer>
    </div>
  </div>
  <div class="modal-backdrop" id="modal-termos" role="dialog" aria-modal="true" aria-labelledby="titulo-termos" hidden>
    <div class="modal"><span class="eyebrow">Prospera</span><h2 id="titulo-termos"><?= Visao::escapar($tradutor->obter('termos')) ?></h2>
      <div class="terms-box">Ao utilizar o Prospera, você entende que esta é uma ferramenta organizacional e educacional. Ela não oferece aconselhamento financeiro, contábil ou jurídico.</div>
      <label class="check"><input id="confirmacao-termos" type="checkbox"><span><?= Visao::escapar($tradutor->obter('aceite')) ?></span></label>
      <button class="btn btn-primary" id="aceitar-termos" disabled><?= Visao::escapar($tradutor->obter('aceitar')) ?></button>
    </div>
  </div>
  <div class="toast" id="aviso" hidden></div>
  <script src="js/principal.js?v=6" defer></script>
</body>
</html>
