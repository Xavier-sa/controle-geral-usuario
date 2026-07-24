<?php
$minhaLinha = current(array_filter($ranking, static fn(array $linha): bool => $linha['usuario']->obter('usuario_id') === $usuarioAtual->obter('usuario_id'))) ?: null;
?>
<section>
  <div class="section-title">
    <div><h1>Ranking da comunidade</h1><p>Cada perfil concluído por alguém que você convidou vale uma estrela.</p></div>
    <?php if ($podeConvidar): ?><a class="btn btn-primary" href="?pagina=usuario_formulario">Convidar pessoa</a><?php endif; ?>
  </div>
  <?php if (isset($_GET['convidado'])): ?><div class="mensagem-sucesso" role="status">Convite criado. A estrela será liberada quando a pessoa completar o primeiro acesso.</div><?php endif; ?>
  <?php if (!$podeConvidar): ?><div class="mensagem-bloqueio">Complete sua renda e empresa no primeiro acesso para liberar novos convites.</div><?php endif; ?>
  <?php if ($minhaLinha && $minhaLinha['estrelas'] > 0): ?>
    <div class="panel destaque-popular"><span aria-hidden="true">★</span><div><strong>Você é popular demais!</strong><p>Seus convites já ajudaram <?= $minhaLinha['estrelas'] ?> <?= $minhaLinha['estrelas'] === 1 ? 'pessoa' : 'pessoas' ?> a organizar a vida financeira.</p></div></div>
  <?php endif; ?>
  <div class="panel table-wrap">
    <table class="data-table">
      <thead><tr><th>Posição</th><th>Pessoa</th><th>Estrelas</th><th>Entrada</th><?php if ($usuarioAtual->ehAdministrador()): ?><th>Ações administrativas</th><?php endif; ?></tr></thead>
      <tbody>
      <?php foreach ($ranking as $linha): $usuario=$linha['usuario']; ?>
        <tr class="<?= $usuario->obter('usuario_id')===$usuarioAtual->obter('usuario_id')?'linha-atual':'' ?>">
          <td><strong>#<?= $linha['posicao'] ?></strong></td>
          <td><strong><?= Visao::escapar($usuario->obter('apelido') ?: $usuario->obter('nome')) ?></strong><?= $usuario->ehAdministrador() ? ' <span class="badge">ADM</span>' : '' ?></td>
          <td><span class="estrelas-ranking">★ <?= $linha['estrelas'] ?></span></td>
          <td><?= Visao::escapar(date('m/Y', strtotime((string)$usuario->obter('criado_em')))) ?></td>
          <?php if ($usuarioAtual->ehAdministrador()): ?><td><div class="acoes-tabela"><a class="action-btn" href="?pagina=usuario_formulario&amp;id=<?= Visao::escapar($usuario->obter('usuario_id')) ?>">Editar</a><?php if($usuario->obter('usuario_id')!==$usuarioAtual->obter('usuario_id')):?><form method="post" onsubmit="return confirm('Excluir este usuário?')"><input type="hidden" name="token" value="<?= Visao::escapar($token) ?>"><input type="hidden" name="acao" value="excluir_usuario"><input type="hidden" name="usuario_id" value="<?= Visao::escapar($usuario->obter('usuario_id')) ?>"><button class="action-btn perigo">Excluir</button></form><?php endif;?></div></td><?php endif; ?>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <p class="nota-privacidade">Por privacidade, renda, empresa e e-mail não aparecem no ranking.</p>
</section>
