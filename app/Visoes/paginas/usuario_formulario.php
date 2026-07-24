<?php $editando=$usuarioEdicao!==null; ?>
<section>
  <div class="section-title"><div><h1><?= $editando?'Editar usuário':'Convidar nova pessoa' ?></h1><p><?= $editando?'Altere os dados administrativos.':'Somente pessoas convidadas podem acessar o Prospera.' ?></p></div></div>
  <?php if (($_GET['erro'] ?? '') === 'email'): ?><div class="mensagem-erro" role="alert">Este e-mail já pertence a outro usuário.</div><?php endif; ?>
  <form method="post" class="panel formulario-cadastro">
    <input type="hidden" name="token" value="<?= Visao::escapar($token) ?>"><input type="hidden" name="acao" value="salvar_usuario"><input type="hidden" name="usuario_id" value="<?= $editando?Visao::escapar($usuarioEdicao->obter('usuario_id')):'' ?>">
    <label>Nome<input name="nome" required maxlength="100" value="<?= $editando?Visao::escapar($usuarioEdicao->obter('nome')):'' ?>"></label>
    <label>Apelido público<input name="apelido" required maxlength="40" value="<?= $editando?Visao::escapar($usuarioEdicao->obter('apelido')):'' ?>" placeholder="Nome exibido no ranking"></label>
    <label>E-mail<input type="email" name="email" required value="<?= $editando?Visao::escapar($usuarioEdicao->obter('email')):'' ?>"></label>
    <label>Senha temporária <?= $editando?'<small>(deixe vazia para manter)</small>':'' ?><input type="password" name="senha" <?= $editando?'':'required' ?> minlength="8"></label>
    <?php if ($editando && $usuarioAtual->ehAdministrador()): ?>
      <label>Perfil<select name="perfil"><option value="membro" <?= $usuarioEdicao->obter('perfil')==='membro'?'selected':'' ?>>Membro</option><option value="administrador" <?= $usuarioEdicao->obter('perfil')==='administrador'?'selected':'' ?>>Administrador</option></select></label>
      <label>Status<select name="status"><option value="ativo" <?= $usuarioEdicao->obter('status')==='ativo'?'selected':'' ?>>Ativo</option><option value="inativo" <?= $usuarioEdicao->obter('status')==='inativo'?'selected':'' ?>>Inativo</option></select></label>
    <?php endif; ?>
    <div class="aviso-privacidade">Informe à pessoa que ela receberá acesso ao Prospera. Não cadastre dados sem o conhecimento dela.</div>
    <div class="acoes-formulario"><a class="btn btn-ghost" href="?pagina=usuarios">Cancelar</a><button class="btn btn-primary"><?= $editando?'Salvar alterações':'Criar convite' ?></button></div>
  </form>
</section>
