CREATE TABLE IF NOT EXISTS convites (
    convite_id VARCHAR(36) PRIMARY KEY,
    indicador_usuario_id VARCHAR(36) NOT NULL,
    usuario_criado_id VARCHAR(36) NULL,
    token_hash CHAR(64) NOT NULL,
    codigo_hash CHAR(64) NOT NULL,
    codigo_final CHAR(4) NOT NULL,
    status ENUM('pendente','utilizado','revogado') NOT NULL DEFAULT 'pendente',
    expira_em DATETIME NOT NULL,
    usado_em DATETIME NULL,
    criado_em DATETIME NOT NULL,
    atualizado_em DATETIME NOT NULL,
    UNIQUE KEY uq_convites_token_hash (token_hash),
    UNIQUE KEY uq_convites_codigo_hash (codigo_hash),
    KEY idx_convites_indicador_status (indicador_usuario_id, status),
    KEY idx_convites_expiracao (status, expira_em),
    CONSTRAINT fk_convites_indicador FOREIGN KEY (indicador_usuario_id)
        REFERENCES usuarios (usuario_id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_convites_usuario_criado FOREIGN KEY (usuario_criado_id)
        REFERENCES usuarios (usuario_id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
