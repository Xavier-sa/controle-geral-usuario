<?php
$meses = [1=>'Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];
$custoNoMes = static function($custo, string $mes): bool {
    $inicio = substr((string)($custo->obter('recorrencia_inicio') ?: date('Y-01-01')), 0, 7);
    $fimValor = $custo->obter('recorrencia_fim');
    $fim = $fimValor ? substr((string)$fimValor, 0, 7) : null;
    $recorrente = $custo->obter('recorrente') === null ? true : (bool)$custo->obter('recorrente');
    if (!$recorrente) return $mes === $inicio;
    return $mes >= $inicio && ($fim === null || $mes <= $fim);
};
?>
<section>
  <div class="section-title"><div><h1>Calendário anual de gastos</h1><p>Projete despesas mensais que continuam até você definir um término ou removê-las.</p></div><div class="navegacao-ano"><a class="action-btn" href="?pagina=calendario&amp;ano=<?= $anoCalendario-1 ?>">←</a><strong><?= $anoCalendario ?></strong><a class="action-btn" href="?pagina=calendario&amp;ano=<?= $anoCalendario+1 ?>">→</a></div></div>
  <?php if(isset($_GET['salvo'])):?><div class="mensagem-sucesso" role="status">Recorrência atualizada.</div><?php endif; ?>
  <?php if(isset($_GET['erro'])):?><div class="mensagem-erro" role="alert">Revise o período informado. A data final não pode ser anterior ao início.</div><?php endif; ?>

  <details class="panel configuracao-recorrencias">
    <summary>Configurar repetição dos gastos</summary>
    <p>Deixe “Até” vazio para perpetuar mensalmente. Desmarque a repetição para lançar somente no mês inicial.</p>
    <div class="lista-recorrencias">
      <?php foreach($custos as $custo):
        $inicio=substr((string)($custo->obter('recorrencia_inicio')?:"{$anoCalendario}-01-01"),0,7);
        $fim=$custo->obter('recorrencia_fim')?substr((string)$custo->obter('recorrencia_fim'),0,7):'';
        $recorrente=$custo->obter('recorrente')===null?true:(bool)$custo->obter('recorrente');
      ?>
      <form method="post" class="item-recorrencia">
        <input type="hidden" name="token" value="<?= Visao::escapar($token) ?>"><input type="hidden" name="acao" value="salvar_recorrencia"><input type="hidden" name="custo_id" value="<?= Visao::escapar($custo->obter('custo_fixo_id')) ?>"><input type="hidden" name="ano" value="<?= $anoCalendario ?>">
        <strong><?= Visao::escapar($custo->obter('nome')) ?></strong>
        <label class="check-recorrencia"><input type="checkbox" name="recorrente" value="1" <?= $recorrente?'checked':'' ?>> Repetir mensalmente</label>
        <label>Desde<input type="month" name="inicio" value="<?= Visao::escapar($inicio) ?>" required></label>
        <label>Até <small>(opcional)</small><input type="month" name="fim" value="<?= Visao::escapar($fim) ?>"></label>
        <label>Vencimento<input type="number" name="dia_vencimento" min="1" max="31" value="<?= Visao::escapar($custo->obter('dia_vencimento')) ?>" placeholder="Dia"></label>
        <button class="btn btn-primary">Salvar</button>
      </form>
      <?php endforeach; ?>
    </div>
  </details>

  <div class="grade-calendario-anual">
    <?php foreach($meses as $numero=>$nome): $mes=sprintf('%04d-%02d',$anoCalendario,$numero);$totalMes=0;$itensMes=[];foreach($custos as $custo){if(!$custoNoMes($custo,$mes))continue;$valor=(float)$custo->obter('valor_mensal');$totalMes+=$valor;$itensMes[]=[$custo,$valor];}$mesAtual=$mes===date('Y-m'); ?>
    <article class="panel mes-calendario <?= $mesAtual?'mes-atual':'' ?>">
      <div class="cabecalho-mes"><div><span><?= str_pad((string)$numero,2,'0',STR_PAD_LEFT) ?></span><h2><?= $nome ?></h2></div><strong><?= Visao::moeda($totalMes,$idioma) ?></strong></div>
      <div class="gastos-mes">
        <?php foreach($itensMes as [$custo,$valor]): ?><div class="gasto-calendario"><span><?= Visao::escapar($custo->obter('nome')) ?><?php if($custo->obter('dia_vencimento')): ?><small>dia <?= (int)$custo->obter('dia_vencimento') ?></small><?php endif; ?></span><strong class="<?= $valor<=0?'pendente':'' ?>"><?= $valor>0?Visao::moeda($valor,$idioma):'Pendente' ?></strong></div><?php endforeach; ?>
        <?php if($itensMes===[]):?><p class="empty">Nenhum gasto previsto.</p><?php endif; ?>
      </div>
    </article>
    <?php endforeach; ?>
  </div>
</section>
