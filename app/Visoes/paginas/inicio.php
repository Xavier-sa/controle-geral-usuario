<section>
  <?php
  $evolucaoUsuarios=[];
  if($usuarioAtual->ehAdministrador()){
    for($i=11;$i>=0;$i--){
      $inicioMes=new DateTimeImmutable("first day of -{$i} month 00:00:00");
      $fimMes=$inicioMes->modify('last day of this month 23:59:59');
      $novos=count(array_filter($usuarios,static fn($u):bool=>strtotime((string)$u->obter('criado_em'))>=$inicioMes->getTimestamp()&&strtotime((string)$u->obter('criado_em'))<=$fimMes->getTimestamp()));
      $acumulado=count(array_filter($usuarios,static fn($u):bool=>strtotime((string)$u->obter('criado_em'))<=$fimMes->getTimestamp()));
      $evolucaoUsuarios[]=['rotulo'=>$inicioMes->format('m/y'),'novos'=>$novos,'acumulado'=>$acumulado];
    }
  }
  $maiorEntrada=max(1,...array_column($evolucaoUsuarios,'novos'));
  ?>
  <div class="hero"><div><span class="eyebrow"><?= Visao::escapar($tradutor->obter('futuro')) ?></span><h1><?= Visao::escapar($tradutor->obter('titulo')) ?></h1><p><?= Visao::escapar($tradutor->obter('descricao')) ?></p>
    <div class="button-row"><a class="btn btn-primary" href="?pagina=custos&amp;idioma=<?= $idioma ?>"><?= Visao::escapar($tradutor->obter('ver_custos')) ?></a><a class="btn btn-secondary" href="?pagina=indicacao&amp;idioma=<?= $idioma ?>"><?= Visao::escapar($tradutor->obter('indicar_alguem')) ?></a></div></div></div>
  <div class="stats">
    <article class="stat"><div class="stat-label"><?= Visao::escapar($tradutor->obter('renda')) ?></div><div class="stat-value"><?= Visao::moeda((float) $resumo->obter('renda_mensal'), $idioma) ?></div></article>
    <article class="stat"><div class="stat-label"><?= Visao::escapar($tradutor->obter('custos_fixos')) ?></div><div class="stat-value"><?= Visao::moeda((float) $resumo->obter('custos_fixos'), $idioma) ?></div></article>
    <article class="stat"><div class="stat-label"><?= Visao::escapar($tradutor->obter('saldo')) ?></div><div class="stat-value"><?= Visao::moeda((float) $resumo->obter('saldo_projetado'), $idioma) ?></div></article>
    <article class="stat"><div class="stat-label"><?= Visao::escapar($tradutor->obter('ativos')) ?></div><div class="stat-value"><?= count(array_filter($usuarios, static fn ($usuario) => $usuario->obter('status') === 'ativo')) ?></div></article>
  </div>
  <?php if($usuarioAtual->ehAdministrador()): $entradasMesAtual=$evolucaoUsuarios[array_key_last($evolucaoUsuarios)]['novos']??0; ?>
  <section class="panel painel-evolucao-usuarios" aria-labelledby="titulo-evolucao-usuarios">
    <div class="panel-head"><div><h2 id="titulo-evolucao-usuarios">Evolução de usuários</h2><p>Novos integrantes por mês e total acumulado nos últimos 12 meses.</p></div><div class="resumo-evolucao"><span><small>Total</small><strong><?= count($usuarios) ?></strong></span><span><small>Este mês</small><strong>+<?= $entradasMesAtual ?></strong></span></div></div>
    <div class="legenda-grafico"><span><i class="legenda-barra"></i>Novos usuários</span><span><i class="legenda-total"></i>Total acumulado</span></div>
    <div class="grafico-usuarios" role="img" aria-label="Gráfico de evolução de usuários nos últimos doze meses">
      <?php foreach($evolucaoUsuarios as $mes): $altura=$mes['novos']>0?max(8,(int)round($mes['novos']/$maiorEntrada*100)):2; ?>
      <div class="coluna-grafico" title="<?= $mes['rotulo'] ?>: <?= $mes['novos'] ?> novos, <?= $mes['acumulado'] ?> no total">
        <span class="total-acumulado"><?= $mes['acumulado'] ?></span>
        <div class="area-barra"><span class="barra-usuarios <?= $mes['novos']===0?'sem-entrada':'' ?>" style="height:<?= $altura ?>%"><b><?= $mes['novos'] ?></b></span></div>
        <small><?= $mes['rotulo'] ?></small>
      </div>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>
  <div class="grid-2"><section class="panel"><div class="panel-head"><h2><?= Visao::escapar($tradutor->obter('custos_mes')) ?></h2><a class="action-btn" href="?pagina=custos&amp;idioma=<?= $idioma ?>">Ver todos →</a></div>
    <?php $custosExibidos = array_slice($custos, 0, 4); require dirname(__DIR__) . '/componentes/lista-custos.php'; ?>
  </section><section class="panel quote-card"><span class="eyebrow"><?= Visao::escapar($tradutor->obter('meta')) ?></span><blockquote><?= Visao::escapar($tradutor->obter('frase')) ?></blockquote><div class="progress"><span style="width:<?= (int) $resumo->obter('percentual_meta') ?>%"></span></div><small><?= (int) $resumo->obter('percentual_meta') ?>%</small></section></div>
</section>
<?php require dirname(__DIR__) . '/componentes/galeria-prosperidade.php'; ?>
