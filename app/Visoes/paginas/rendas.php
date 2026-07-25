<?php
$tiposRenda = [
    'salario' => 'Salário',
    'aplicativo' => 'Aplicativo (Uber, 99Pop, inDrive)',
    'autonomo' => 'Trabalho autônomo',
    'bico' => 'Bicos e renda eventual',
    'beneficio' => 'Benefício ou aposentadoria',
    'outros' => 'Outros',
];
$totalRenda = array_reduce($fontesRenda, static fn(float $total, $fonte): float => $total + (float)$fonte->obter('valor_mensal'), 0.0);
?>
<section>
  <div class="section-title"><div><h1>Minhas rendas</h1><p>Cadastre todas as suas fontes para manter o saldo projetado próximo da realidade.</p></div><div class="total-rendas"><small>Renda mensal total</small><strong><?= Visao::moeda($totalRenda, $idioma) ?></strong></div></div>
  <?php if (isset($_GET['adicionada'])): ?><div class="mensagem-sucesso" role="status">Fonte de renda adicionada.</div><?php endif; ?>
  <?php if (isset($_GET['salva'])): ?><div class="mensagem-sucesso" role="status">Fonte de renda atualizada.</div><?php endif; ?>
  <?php if (isset($_GET['excluida'])): ?><div class="mensagem-sucesso" role="status">Fonte de renda removida e total recalculado.</div><?php endif; ?>
  <?php if (isset($_GET['erro'])): ?><div class="mensagem-erro" role="alert">Informe um nome e um valor mensal maior que zero.</div><?php endif; ?>
  <?php if (isset($_GET['ultima'])): ?><div class="mensagem-bloqueio" role="alert">Mantenha pelo menos uma fonte de renda. Você pode editar a fonte atual.</div><?php endif; ?>

  <form method="post" class="panel formulario-nova-renda">
    <input type="hidden" name="token" value="<?= Visao::escapar($token) ?>"><input type="hidden" name="acao" value="adicionar_renda">
    <div class="panel-head"><div><h2>Adicionar fonte de renda</h2><p>Use uma média mensal para rendas variáveis, como aplicativos e bicos.</p></div></div>
    <div class="grade-renda">
      <label>Fonte<input name="nome" required maxlength="80" placeholder="Ex.: Uber aos finais de semana"></label>
      <label>Tipo<select name="tipo" required><?php foreach($tiposRenda as $valor=>$rotulo): ?><option value="<?= $valor ?>"><?= Visao::escapar($rotulo) ?></option><?php endforeach; ?></select></label>
      <label>Valor mensal (R$)<input name="valor_mensal" required inputmode="decimal" placeholder="0,00"></label>
      <button class="btn btn-primary">Adicionar renda</button>
    </div>
  </form>

  <div class="lista-rendas">
    <?php foreach ($fontesRenda as $fonte): ?>
      <form method="post" class="panel item-renda">
        <input type="hidden" name="token" value="<?= Visao::escapar($token) ?>"><input type="hidden" name="fonte_renda_id" value="<?= Visao::escapar($fonte->obter('fonte_renda_id')) ?>">
        <label>Fonte<input name="nome" required maxlength="80" value="<?= Visao::escapar($fonte->obter('nome')) ?>"></label>
        <label>Tipo<select name="tipo"><?php foreach($tiposRenda as $valor=>$rotulo): ?><option value="<?= $valor ?>" <?= $fonte->obter('tipo')===$valor?'selected':'' ?>><?= Visao::escapar($rotulo) ?></option><?php endforeach; ?></select></label>
        <label>Valor mensal (R$)<input name="valor_mensal" required inputmode="decimal" value="<?= number_format((float)$fonte->obter('valor_mensal'),2,',','.') ?>"></label>
        <div class="acoes-renda"><button class="btn btn-primary" name="acao" value="atualizar_renda">Salvar</button><button class="btn btn-ghost perigo" name="acao" value="excluir_renda" formnovalidate onclick="return confirm('Remover esta fonte de renda?')">Remover</button></div>
      </form>
    <?php endforeach; ?>
  </div>
</section>
