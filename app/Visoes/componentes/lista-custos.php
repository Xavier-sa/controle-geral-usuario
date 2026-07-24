<div class="cost-list">
  <?php foreach ($custosExibidos as $custo): ?>
    <article class="cost-row"><div class="cost-icon" aria-hidden="true"><?= Visao::escapar($custo->obter('icone')) ?></div>
      <div><h3><?= Visao::escapar($custo->obter('nome')) ?></h3><p><?= Visao::escapar($custo->obter('categoria')) ?></p></div>
      <strong class="amount"><?= Visao::moeda((float) $custo->obter('valor_mensal'), $idioma) ?></strong>
    </article>
  <?php endforeach; ?>
</div>
