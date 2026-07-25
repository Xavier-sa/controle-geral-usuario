INSERT INTO fontes_renda (
    fonte_renda_id, usuario_id, nome, tipo, valor_mensal, ativo, criado_em, atualizado_em
)
SELECT
    UUID(), u.usuario_id, 'Renda principal', 'salario', u.renda_mensal, TRUE,
    COALESCE(u.perfil_completo_em, u.criado_em), NOW()
FROM usuarios u
WHERE u.renda_mensal > 0
  AND NOT EXISTS (
      SELECT 1 FROM fontes_renda f WHERE f.usuario_id = u.usuario_id
  );
