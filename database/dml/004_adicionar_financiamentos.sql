INSERT INTO custos_mensais (
    custo_fixo_id, usuario_id, nome, categoria, icone, valor_mensal,
    situacao, moeda, ativo, personalizado, criado_em, atualizado_em
)
SELECT UUID(), u.usuario_id, modelo.nome, 'financiamentos', modelo.icone, 0,
       'pendente', 'BRL', TRUE, FALSE, NOW(), NOW()
FROM usuarios u
CROSS JOIN (
    SELECT 'Financiamento da casa' AS nome, '⌂' AS icone
    UNION ALL SELECT 'Financiamento do carro', '▣'
    UNION ALL SELECT 'Financiamento da moto', '◇'
) modelo
WHERE NOT EXISTS (
    SELECT 1
    FROM custos_mensais custo
    WHERE custo.usuario_id = u.usuario_id
      AND custo.nome = modelo.nome
      AND custo.ativo = TRUE
);
