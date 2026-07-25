<section class="onboarding">
  <div class="panel onboarding-card">
    <span class="eyebrow">Primeiro acesso</span>
    <h1>Complete seu perfil financeiro</h1>
    <p>Cadastre sua primeira fonte de renda para calcular o saldo projetado. Depois você poderá adicionar Uber, 99Pop, inDrive, bicos e outras rendas. Esses dados não aparecem no ranking nem para quem convidou você.</p>
    <?php if (isset($_GET['erro'])): ?><div class="mensagem-erro" role="alert">Revise os campos e confirme a ciência sobre o uso dos dados.</div><?php endif; ?>
    <form method="post" class="formulario-onboarding">
      <input type="hidden" name="token" value="<?= Visao::escapar($token) ?>"><input type="hidden" name="acao" value="completar_perfil">
      <label>Valor mensal desta renda <span>R$</span><input name="renda_mensal" inputmode="decimal" required placeholder="0,00"></label>
      <label>Empresa ou nome da fonte<input name="empresa" required maxlength="100" placeholder="Ex.: Empresa X, Uber ou bicos"></label>
      <label>Tipo de renda<select name="tipo_renda" required><option value="salario">Salário</option><option value="aplicativo">Aplicativo (Uber, 99Pop, inDrive)</option><option value="autonomo">Trabalho autônomo</option><option value="bico">Bicos e renda eventual</option><option value="beneficio">Benefício ou aposentadoria</option><option value="outros">Outros</option></select></label>
      <div class="caixa-lgpd"><strong>Como cuidamos desses dados</strong><p>Finalidade: produzir seu planejamento financeiro e habilitar convites. Acesso: somente você e administradores autorizados. Não exibimos renda ou empresa no ranking. Você pode solicitar acesso, correção ou exclusão pelo canal de suporte.</p></div>
      <label class="check"><input type="checkbox" name="aceite_privacidade" value="1" required><span>Li e estou ciente do tratamento dos meus dados para essas finalidades.</span></label>
      <button class="btn btn-primary">Salvar e continuar</button>
    </form>
  </div>
</section>
