<section>
  <div class="hero"><div><span class="eyebrow"><?= Visao::escapar($tradutor->obter('futuro')) ?></span><h1><?= Visao::escapar($tradutor->obter('titulo')) ?></h1><p><?= Visao::escapar($tradutor->obter('descricao')) ?></p>
    <div class="button-row"><a class="btn btn-primary" href="?pagina=custos&amp;idioma=<?= $idioma ?>"><?= Visao::escapar($tradutor->obter('ver_custos')) ?></a><a class="btn btn-secondary" href="?pagina=indicacao&amp;idioma=<?= $idioma ?>"><?= Visao::escapar($tradutor->obter('indicar_alguem')) ?></a></div></div></div>
  <div class="stats">
    <article class="stat"><div class="stat-label"><?= Visao::escapar($tradutor->obter('renda')) ?></div><div class="stat-value"><?= Visao::moeda((float) $resumo->obter('renda_mensal'), $idioma) ?></div></article>
    <article class="stat"><div class="stat-label"><?= Visao::escapar($tradutor->obter('custos_fixos')) ?></div><div class="stat-value"><?= Visao::moeda((float) $resumo->obter('custos_fixos'), $idioma) ?></div></article>
    <article class="stat"><div class="stat-label"><?= Visao::escapar($tradutor->obter('saldo')) ?></div><div class="stat-value"><?= Visao::moeda((float) $resumo->obter('saldo_projetado'), $idioma) ?></div></article>
    <article class="stat"><div class="stat-label"><?= Visao::escapar($tradutor->obter('ativos')) ?></div><div class="stat-value"><?= count(array_filter($usuarios, static fn ($usuario) => $usuario->obter('status') === 'ativo')) ?></div></article>
  </div>
  <div class="grid-2"><section class="panel"><div class="panel-head"><h2><?= Visao::escapar($tradutor->obter('custos_mes')) ?></h2><a class="action-btn" href="?pagina=custos&amp;idioma=<?= $idioma ?>">Ver todos →</a></div>
    <?php $custosExibidos = array_slice($custos, 0, 4); require dirname(__DIR__) . '/componentes/lista-custos.php'; ?>
  </section><section class="panel quote-card"><span class="eyebrow"><?= Visao::escapar($tradutor->obter('meta')) ?></span><blockquote><?= Visao::escapar($tradutor->obter('frase')) ?></blockquote><div class="progress"><span style="width:<?= (int) $resumo->obter('percentual_meta') ?>%"></span></div><small><?= (int) $resumo->obter('percentual_meta') ?>%</small></section></div>
</section>
<?php require dirname(__DIR__) . '/componentes/galeria-prosperidade.php'; ?>
