UPDATE custos_mensais
SET recorrente = TRUE,
    recorrencia_inicio = CONCAT(YEAR(CURRENT_DATE), '-01-01'),
    recorrencia_fim = NULL
WHERE recorrencia_inicio IS NULL;
