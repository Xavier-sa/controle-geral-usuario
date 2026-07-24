<section class="onboarding">
  <div class="panel onboarding-card">
    <span class="eyebrow">Primeiro acesso</span>
    <h1>Complete seu perfil financeiro</h1>
    <p>Usaremos sua renda apenas para calcular seu saldo projetado. A empresa ajuda a contextualizar sua fonte de renda. Esses dados não aparecem no ranking nem para quem convidou você.</p>
    <?php if (isset($_GET['erro'])): ?><div class="mensagem-erro" role="alert">Revise os campos e confirme a ciência sobre o uso dos dados.</div><?php endif; ?>
    <form method="post" class="formulario-onboarding">
      <input type="hidden" name="token" value="<?= Visao::escapar($token) ?>"><input type="hidden" name="acao" value="completar_perfil">
      <label>Quanto você ganha por mês? <span>R$</span><input name="renda_mensal" inputmode="decimal" required placeholder="0,00"></label>
      <label>Empresa ou fonte de renda<input name="empresa" required maxlength="100" placeholder="Ex.: Empresa X, autônomo ou aposentadoria"></label>
      <div class="caixa-lgpd"><strong>Como cuidamos desses dados</strong><p>Finalidade: produzir seu planejamento financeiro e habilitar convites. Acesso: somente você e administradores autorizados. Não exibimos renda ou empresa no ranking. Você pode solicitar acesso, correção ou exclusão pelo canal de suporte.</p></div>
      <label class="check"><input type="checkbox" name="aceite_privacidade" value="1" required><span>Li e estou ciente do tratamento dos meus dados para essas finalidades.</span></label>
      <button class="btn btn-primary">Salvar e continuar</button>
    </form>
  </div>
</section>
