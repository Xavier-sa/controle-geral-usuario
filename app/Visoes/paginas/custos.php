<?php
$quantidadeCustos = count($custos);
$quantidadePreenchida = count(array_filter($custos, static fn ($custo): bool => in_array($custo->obter('situacao'), ['com_gasto','sem_gasto'], true)));
$percentualPreenchido = $quantidadeCustos > 0 ? (int) round($quantidadePreenchida / $quantidadeCustos * 100) : 0;
$classeProgresso = $percentualPreenchido < 40 ? 'inicio' : ($percentualPreenchido < 100 ? 'andamento' : 'completo');
?>
<section>
  <div class="section-title"><div><h1><?= Visao::escapar($tradutor->obter('custos_fixos')) ?></h1><p><?= Visao::escapar($tradutor->obter('preencha_custos')) ?></p></div></div>

  <?php if (isset($_GET['bloqueado'])): ?><div class="mensagem-bloqueio" role="alert">Complete todas as categorias para liberar a área de prosperidade.</div><?php endif; ?>
  <?php if (isset($_GET['salvo'])): ?><div class="mensagem-sucesso" role="status"><?= Visao::escapar($tradutor->obter('custos_salvos')) ?></div><?php endif; ?>
  <?php if (isset($_GET['adicionado'])): ?><div class="mensagem-sucesso" role="status">Despesa adicionada e incluída no seu saldo projetado.</div><?php endif; ?>
  <?php if (isset($_GET['excluido'])): ?><div class="mensagem-sucesso" role="status">Despesa personalizada removida.</div><?php endif; ?>
  <?php if (isset($_GET['erro_custo'])): ?><div class="mensagem-erro" role="alert">Preencha nome, categoria e um valor mensal maior que zero.</div><?php endif; ?>

  <form method="post" class="panel formulario-novo-custo">
    <input type="hidden" name="token" value="<?= Visao::escapar($token) ?>">
    <input type="hidden" name="acao" value="adicionar_custo">
    <div class="panel-head"><div><h2>Adicionar despesa do dia a dia</h2><p>Registre uma estimativa mensal para café da manhã, doces, cerveja, cigarro, lazer ou qualquer outro gasto.</p></div></div>
    <div class="grade-novo-custo">
      <label>Despesa
        <select name="nome" id="seletor-despesa" required>
          <option value="" selected disabled>Selecione uma despesa</option>
          <option value="Café da manhã">Café da manhã</option>
          <option value="Cerveja e bebidas">Cerveja e bebidas</option>
          <option value="Cigarro">Cigarro</option>
          <option value="Doces e lanches">Doces e lanches</option>
          <option value="Delivery">Delivery</option>
          <option value="Restaurantes">Restaurantes</option>
          <option value="Financiamento da casa">Financiamento da casa</option>
          <option value="Financiamento do carro">Financiamento do carro</option>
          <option value="Financiamento da moto">Financiamento da moto</option>
          <option value="Jogos e apostas">Jogos e apostas (Tigrinho, bets e loterias)</option>
          <option value="Academia">Academia</option>
          <option value="Medicamentos">Medicamentos</option>
          <option value="Streaming">Streaming</option>
          <option value="outro">Outra despesa…</option>
        </select>
      </label>
      <label id="campo-outra-despesa" hidden>Qual despesa?
        <input name="nome_outro" id="nome-outra-despesa" maxlength="80" placeholder="Digite o nome da despesa">
      </label>
      <label>Categoria
        <select name="categoria" id="seletor-categoria" required>
          <option value="" selected disabled>Selecione uma categoria</option>
          <option value="alimentação">Alimentação</option>
          <option value="assinaturas">Assinaturas</option>
          <option value="cuidados pessoais">Cuidados pessoais</option>
          <option value="educação">Educação</option>
          <option value="financiamentos">Financiamentos</option>
          <option value="hábitos pessoais">Hábitos pessoais</option>
          <option value="jogos e apostas">Jogos e apostas</option>
          <option value="lazer">Lazer</option>
          <option value="saúde">Saúde</option>
          <option value="transporte">Transporte</option>
          <option value="vestuário">Vestuário</option>
          <option value="outros">Outros</option>
        </select>
      </label>
      <label>Valor mensal (R$)<input name="valor" inputmode="decimal" required placeholder="0,00"></label>
      <button class="btn btn-primary" type="submit">Adicionar despesa</button>
    </div>
  </form>

  <div class="panel painel-progresso">
    <div class="panel-head"><div><h2><?= Visao::escapar($tradutor->obter('progresso_preenchimento')) ?></h2><p id="texto-progresso"><?= $quantidadePreenchida ?> de <?= $quantidadeCustos ?> <?= Visao::escapar($tradutor->obter('campos_preenchidos')) ?></p></div><strong id="percentual-progresso"><?= $percentualPreenchido ?>%</strong></div>
    <div class="barra-preenchimento <?= $classeProgresso ?>" id="barra-preenchimento" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= $percentualPreenchido ?>"><span style="width:<?= $percentualPreenchido ?>%"></span></div>
  </div>

  <form method="post" class="panel formulario-custos" id="formulario-custos">
    <input type="hidden" name="token" value="<?= Visao::escapar($token) ?>"><input type="hidden" name="acao" value="salvar_custos">
    <?php foreach ($custos as $custo): ?>
      <div class="campo-custo"><span class="cost-icon" aria-hidden="true"><?= Visao::escapar($custo->obter('icone')) ?></span><span class="dados-custo"><strong><?= Visao::escapar($custo->obter('nome')) ?></strong><small><?= Visao::escapar($custo->obter('categoria')) ?><?= $custo->obter('personalizado') ? ' · personalizada' : '' ?></small></span><div class="resposta-custo"><select data-situacao-custo name="situacoes[<?= Visao::escapar($custo->obter('custo_fixo_id')) ?>]" aria-label="Situação de <?= Visao::escapar($custo->obter('nome')) ?>"><option value="pendente" <?= $custo->obter('situacao')==='pendente'?'selected':'' ?>>Selecione</option><option value="com_gasto" <?= $custo->obter('situacao')==='com_gasto'?'selected':'' ?>>Tenho gasto</option><option value="sem_gasto" <?= $custo->obter('situacao')==='sem_gasto'?'selected':'' ?>>Não tenho gasto aqui</option></select><span class="entrada-monetaria"><span>R$</span><input data-campo-custo type="text" name="valores[<?= Visao::escapar($custo->obter('custo_fixo_id')) ?>]" value="<?= (float)$custo->obter('valor_mensal') > 0 ? number_format((float) $custo->obter('valor_mensal'), 2, ',', '.') : '' ?>" inputmode="decimal" placeholder="0,00" <?= $custo->obter('situacao')==='sem_gasto'?'disabled':'' ?>></span><?php if ($custo->obter('personalizado')): ?><button class="botao-remover-custo" type="submit" name="acao" value="excluir_custo" formaction="" onclick="this.form.custo_id.value='<?= Visao::escapar($custo->obter('custo_fixo_id')) ?>';return confirm('Remover esta despesa?')" aria-label="Remover <?= Visao::escapar($custo->obter('nome')) ?>">×</button><?php endif; ?></div></div>
    <?php endforeach; ?>
    <input type="hidden" name="custo_id" value="">
    <div class="acoes-formulario"><button class="btn btn-primary" type="submit"><?= Visao::escapar($tradutor->obter('salvar_valores')) ?></button></div>
  </form>
</section>
