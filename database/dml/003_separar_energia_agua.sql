INSERT INTO custos_mensais (
    custo_fixo_id, usuario_id, nome, categoria, icone, valor_mensal,
    situacao, moeda, ativo, personalizado, criado_em, atualizado_em
)
SELECT
    UUID(), original.usuario_id, 'Água', 'serviços', '≈', 0,
    'pendente', original.moeda, original.ativo, FALSE, NOW(), NOW()
FROM custos_mensais original
WHERE original.nome = 'Energia e água'
  AND NOT EXISTS (
      SELECT 1
      FROM custos_mensais agua
      WHERE agua.usuario_id = original.usuario_id
        AND agua.nome = 'Água'
        AND agua.ativo = TRUE
  );

UPDATE custos_mensais
SET nome = 'Energia', atualizado_em = NOW()
WHERE nome = 'Energia e água';
