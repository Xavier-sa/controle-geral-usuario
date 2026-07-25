ALTER TABLE custos_mensais
    ADD COLUMN recorrente BOOLEAN NOT NULL DEFAULT TRUE AFTER personalizado,
    ADD COLUMN recorrencia_inicio DATE NULL AFTER recorrente,
    ADD COLUMN recorrencia_fim DATE NULL AFTER recorrencia_inicio,
    ADD COLUMN dia_vencimento TINYINT UNSIGNED NULL AFTER recorrencia_fim,
    ADD KEY idx_custos_recorrencia (usuario_id, recorrente, recorrencia_inicio, recorrencia_fim),
    ADD CONSTRAINT chk_dia_vencimento CHECK (dia_vencimento IS NULL OR dia_vencimento BETWEEN 1 AND 31),
    ADD CONSTRAINT chk_periodo_recorrencia CHECK (recorrencia_fim IS NULL OR recorrencia_fim >= recorrencia_inicio);
