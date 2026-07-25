<?php $editando=$usuarioEdicao!==null; ?>
<section>
<?php if($editando): ?>
  <div class="section-title"><div><h1>Editar usuário</h1><p>Altere somente os dados administrativos necessários.</p></div></div>
  <?php if (($_GET['erro'] ?? '') === 'email'): ?><div class="mensagem-erro" role="alert">Este e-mail já pertence a outro usuário.</div><?php endif; ?>
  <form method="post" class="panel formulario-cadastro">
    <input type="hidden" name="token" value="<?= Visao::escapar($token) ?>"><input type="hidden" name="acao" value="salvar_usuario"><input type="hidden" name="usuario_id" value="<?= Visao::escapar($usuarioEdicao->obter('usuario_id')) ?>">
    <label>Nome<input name="nome" required maxlength="100" value="<?= Visao::escapar($usuarioEdicao->obter('nome')) ?>"></label>
    <label>Apelido público<input name="apelido" required maxlength="40" value="<?= Visao::escapar($usuarioEdicao->obter('apelido')) ?>"></label>
    <label>E-mail<input type="email" name="email" required value="<?= Visao::escapar($usuarioEdicao->obter('email')) ?>"></label>
    <label>Nova senha <small>(deixe vazia para manter)</small><input type="password" name="senha" minlength="8"></label>
    <label>Perfil<select name="perfil"><option value="membro" <?= $usuarioEdicao->obter('perfil')==='membro'?'selected':'' ?>>Membro</option><option value="administrador" <?= $usuarioEdicao->obter('perfil')==='administrador'?'selected':'' ?>>Administrador</option></select></label>
    <label>Status<select name="status"><option value="ativo" <?= $usuarioEdicao->obter('status')==='ativo'?'selected':'' ?>>Ativo</option><option value="inativo" <?= $usuarioEdicao->obter('status')==='inativo'?'selected':'' ?>>Inativo</option></select></label>
    <div class="acoes-formulario"><a class="btn btn-ghost" href="?pagina=usuarios">Cancelar</a><button class="btn btn-primary">Salvar alterações</button></div>
  </form>
<?php else:
  $base=sprintf('%s://%s%s',isset($_SERVER['HTTPS'])?'https':'http',$_SERVER['HTTP_HOST'],strtok($_SERVER['REQUEST_URI'],'?'));
  $linkConvite=$conviteGerado?$base.'?convite='.rawurlencode((string)$conviteGerado['token']):'';
?>
  <div class="section-title"><div><h1>Convidar nova pessoa</h1><p>Gere um acesso seguro. A própria pessoa preencherá os dados pessoais.</p></div><a class="btn btn-ghost" href="?pagina=usuarios">Voltar ao ranking</a></div>
  <?php if($conviteGerado): ?>
    <div class="panel convite-gerado">
      <span class="eyebrow">Convite pronto</span><h2>Compartilhe o link ou o código</h2><p>Este convite é individual, funciona uma vez e expira em sete dias.</p>
      <label>Link do convite<div class="campo-copia"><input id="link-convite-gerado" readonly value="<?= Visao::escapar($linkConvite) ?>"><button class="btn btn-primary" type="button" data-copiar="#link-convite-gerado">Copiar link</button></div></label>
      <label>Código manual<div class="campo-copia"><input id="codigo-convite-gerado" readonly value="<?= Visao::escapar($conviteGerado['codigo']) ?>"><button class="btn btn-secondary" type="button" data-copiar="#codigo-convite-gerado">Copiar código</button></div></label>
      <small>Válido até <?= Visao::escapar(date('d/m/Y H:i',strtotime((string)$conviteGerado['expira_em']))) ?>.</small>
    </div>
  <?php endif; ?>
  <form method="post" class="panel gerar-convite">
    <input type="hidden" name="token" value="<?= Visao::escapar($token) ?>"><input type="hidden" name="acao" value="gerar_convite">
    <div><h2>Gerar novo convite</h2><p>Nenhum nome, e-mail ou senha de terceiro será solicitado.</p></div><button class="btn btn-primary">Gerar link e código</button>
  </form>
  <section class="panel lista-convites"><div class="panel-head"><h2>Meus convites</h2><span class="badge"><?= count($convites) ?></span></div>
    <?php if($convites===[]): ?><p class="empty">Nenhum convite gerado.</p><?php endif; ?>
    <?php foreach($convites as $convite): $expirado=$convite->expirado();$status=$expirado&&$convite->obter('status')==='pendente'?'expirado':$convite->obter('status'); ?>
      <div class="linha-convite"><div><strong>•••••-<?= Visao::escapar($convite->obter('codigo_final')) ?></strong><small>Criado em <?= Visao::escapar(date('d/m/Y H:i',strtotime((string)$convite->obter('criado_em')))) ?></small></div><span class="badge status-convite status-<?= Visao::escapar($status) ?>"><?= Visao::escapar($status) ?></span>
      <?php if($status==='pendente'): ?><form method="post"><input type="hidden" name="token" value="<?= Visao::escapar($token) ?>"><input type="hidden" name="acao" value="revogar_convite"><input type="hidden" name="convite_id" value="<?= Visao::escapar($convite->obter('convite_id')) ?>"><button class="action-btn perigo">Revogar</button></form><?php endif; ?></div>
    <?php endforeach; ?>
  </section>
<?php endif; ?>
</section>
