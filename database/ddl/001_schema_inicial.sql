CREATE TABLE IF NOT EXISTS schema_migrations (
    versao VARCHAR(190) PRIMARY KEY,
    checksum CHAR(64) NOT NULL,
    aplicada_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS usuarios (
    usuario_id VARCHAR(36) PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    apelido VARCHAR(40) NOT NULL,
    email VARCHAR(190) NOT NULL,
    senha_hash VARCHAR(255) NOT NULL,
    perfil ENUM('administrador','membro') NOT NULL DEFAULT 'membro',
    status ENUM('ativo','inativo') NOT NULL DEFAULT 'ativo',
    codigo_indicacao VARCHAR(30) NOT NULL,
    indicado_por_usuario_id VARCHAR(36) NULL,
    locale VARCHAR(10) NOT NULL DEFAULT 'pt-BR',
    empresa VARCHAR(100) NULL,
    renda_mensal DECIMAL(13,2) NULL,
    perfil_completo_em DATETIME NULL,
    termos_aceitos_em DATETIME NULL,
    criado_em DATETIME NOT NULL,
    atualizado_em DATETIME NOT NULL,
    UNIQUE KEY uq_usuarios_email (email),
    UNIQUE KEY uq_usuarios_codigo_indicacao (codigo_indicacao),
    KEY idx_usuarios_indicador (indicado_por_usuario_id),
    CONSTRAINT fk_usuarios_indicador FOREIGN KEY (indicado_por_usuario_id)
        REFERENCES usuarios (usuario_id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS custos_mensais (
    custo_fixo_id VARCHAR(36) PRIMARY KEY,
    usuario_id VARCHAR(36) NOT NULL,
    nome VARCHAR(80) NOT NULL,
    categoria VARCHAR(50) NOT NULL,
    icone VARCHAR(10) NOT NULL DEFAULT '+',
    valor_mensal DECIMAL(13,2) NOT NULL DEFAULT 0,
    situacao ENUM('pendente','com_gasto','sem_gasto') NOT NULL DEFAULT 'pendente',
    moeda CHAR(3) NOT NULL DEFAULT 'BRL',
    ativo BOOLEAN NOT NULL DEFAULT TRUE,
    personalizado BOOLEAN NOT NULL DEFAULT FALSE,
    criado_em DATETIME NOT NULL,
    atualizado_em DATETIME NOT NULL,
    KEY idx_custos_usuario_ativo (usuario_id, ativo),
    CONSTRAINT fk_custos_usuario FOREIGN KEY (usuario_id)
        REFERENCES usuarios (usuario_id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS resumos_financeiros (
    resumo_id VARCHAR(36) PRIMARY KEY,
    usuario_id VARCHAR(36) NOT NULL,
    periodo CHAR(7) NOT NULL,
    renda_mensal DECIMAL(13,2) NOT NULL DEFAULT 0,
    custos_fixos DECIMAL(13,2) NOT NULL DEFAULT 0,
    saldo_projetado DECIMAL(13,2) NOT NULL DEFAULT 0,
    percentual_meta TINYINT UNSIGNED NOT NULL DEFAULT 0,
    moeda CHAR(3) NOT NULL DEFAULT 'BRL',
    criado_em DATETIME NOT NULL,
    atualizado_em DATETIME NOT NULL,
    UNIQUE KEY uq_resumos_usuario_periodo (usuario_id, periodo),
    CONSTRAINT fk_resumos_usuario FOREIGN KEY (usuario_id)
        REFERENCES usuarios (usuario_id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT chk_percentual_meta CHECK (percentual_meta BETWEEN 0 AND 100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS categorias_despesa (
    categoria_id SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(50) NOT NULL,
    nome VARCHAR(80) NOT NULL,
    ativo BOOLEAN NOT NULL DEFAULT TRUE,
    ordem SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    UNIQUE KEY uq_categorias_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
