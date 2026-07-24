<?php
$imagensProsperidade = [
    ['arquivo' => 'broto-patrimonio.svg', 'frase' => $tradutor->obter('frase_broto')],
    ['arquivo' => 'folhas-abundancia.svg', 'frase' => $tradutor->obter('frase_folhas')],
    ['arquivo' => 'colheita-prospera.svg', 'frase' => $tradutor->obter('frase_colheita')]
];
?>
<section class="galeria-prosperidade" aria-labelledby="titulo-galeria">
  <div class="panel-head"><h2 id="titulo-galeria"><?= Visao::escapar($tradutor->obter('caminhos_prosperidade')) ?></h2></div>
  <div class="grade-prosperidade">
    <?php foreach ($imagensProsperidade as $imagem): ?>
      <figure class="imagem-prosperidade">
        <img src="assets/images/<?= Visao::escapar($imagem['arquivo']) ?>?v=2" alt="<?= Visao::escapar($imagem['frase']) ?>">
        <figcaption><?= Visao::escapar($imagem['frase']) ?></figcaption>
      </figure>
    <?php endforeach; ?>
  </div>
</section>
