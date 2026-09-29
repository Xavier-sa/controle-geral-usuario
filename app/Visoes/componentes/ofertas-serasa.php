<?php
$ofertasSerasa = $ofertasSerasa ?? [
    ['logo' => 'WX', 'tipo' => 'Conta atrasada no CPF', 'titulo' => 'DESCONTO EXTRA RECOVERY', 'desconto' => '53%', 'origem' => 'Banco Inter', 'de' => 'R$ 7.333,51', 'por' => 'até 48x R$ 82,93', 'economize' => 'Economize até R$ 3.886,75', 'link' => '#', 'link_text' => 'Ver oferta'],
    ['logo' => 'Neon', 'tipo' => 'Conta atrasada no CPF', 'titulo' => 'OFERTA DESENROLA BRASIL', 'desconto' => '89% de desconto', 'origem' => 'Neon Pagamentos S.A', 'de' => 'R$ 4.088,11', 'por' => 'R$ 437,11', 'economize' => 'Economize até R$ 3.651,00', 'link' => '#', 'link_text' => 'Ver oferta'],
    ['logo' => 'Nu', 'tipo' => 'Dívida negativada no CPF', 'titulo' => 'OFERTA DESENROLA BRASIL', 'desconto' => '86% de desconto', 'origem' => 'Nu Financeira S.A.', 'de' => 'R$ 12.346,17', 'por' => 'R$ 1.728,46', 'economize' => 'Economize até R$ 10.617,71', 'link' => '#', 'link_text' => 'Ver oferta'],
    ['logo' => 'CALCARD', 'tipo' => 'Conta atrasada no CPF', 'titulo' => 'OFERTA DESENROLA BRASIL', 'desconto' => '93% de desconto', 'origem' => 'CALCARD ADMINISTRADORA DE CARTOES LTDA', 'de' => 'R$ 20.676,93', 'por' => 'R$ 5.169,23', 'economize' => 'Economize até R$ 15.507,70', 'link' => '#', 'link_text' => 'Ver oferta']
];
?>
<section class="panel ofertas-serasa" aria-labelledby="titulo-ofertas">
  <div class="panel-head">
    <div>
      <h2 id="titulo-ofertas">Suas ofertas na Serasa</h2>
      <p>Confira propostas exclusivas para quitar suas dívidas, ganhar desconto extra e limpar o CPF.</p>
    </div>
    <span class="badge">Minhas dívidas</span>
  </div>
  <div class="offer-list">
    <?php foreach ($ofertasSerasa as $oferta): ?>
      <article class="offer-card">
        <div class="offer-card-top">
          <div class="offer-indicator">
            <div class="offer-logo"><?= Visao::escapar($oferta['logo']) ?></div>
            <div>
              <p class="offer-type"><?= Visao::escapar($oferta['tipo']) ?></p>
              <h3><?= Visao::escapar($oferta['titulo']) ?></h3>
            </div>
          </div>
          <span class="offer-discount"><?= Visao::escapar($oferta['desconto']) ?></span>
        </div>
        <div class="offer-content">
          <div class="offer-company">
            <span>Origem</span>
            <strong><?= Visao::escapar($oferta['origem']) ?></strong>
          </div>
          <div class="offer-amounts">
            <div class="offer-value"><small>De</small><strong><?= Visao::escapar($oferta['de']) ?></strong></div>
            <div class="offer-value"><small>Por</small><strong><?= Visao::escapar($oferta['por']) ?></strong></div>
          </div>
        </div>
        <div class="offer-footer">
          <a class="btn btn-secondary btn-small" href="<?= Visao::escapar($oferta['link']) ?>"><?= Visao::escapar($oferta['link_text']) ?></a>
          <span class="offer-economy"><?= Visao::escapar($oferta['economize']) ?></span>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</section>
