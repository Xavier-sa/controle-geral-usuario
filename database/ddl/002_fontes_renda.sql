CREATE TABLE IF NOT EXISTS fontes_renda (
    fonte_renda_id VARCHAR(36) PRIMARY KEY,
    usuario_id VARCHAR(36) NOT NULL,
    nome VARCHAR(80) NOT NULL,
    tipo ENUM('salario','aplicativo','autonomo','bico','beneficio','outros') NOT NULL DEFAULT 'outros',
    valor_mensal DECIMAL(13,2) NOT NULL,
    ativo BOOLEAN NOT NULL DEFAULT TRUE,
    criado_em DATETIME NOT NULL,
    atualizado_em DATETIME NOT NULL,
    KEY idx_fontes_renda_usuario_ativo (usuario_id, ativo),
    CONSTRAINT fk_fontes_renda_usuario FOREIGN KEY (usuario_id)
        REFERENCES usuarios (usuario_id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT chk_fonte_renda_valor CHECK (valor_mensal > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
