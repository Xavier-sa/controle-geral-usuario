INSERT INTO categorias_despesa (slug, nome, ordem) VALUES
    ('alimentacao', 'Alimentação', 10),
    ('assinaturas', 'Assinaturas', 20),
    ('cuidados-pessoais', 'Cuidados pessoais', 30),
    ('educacao', 'Educação', 40),
    ('habitos-pessoais', 'Hábitos pessoais', 50),
    ('jogos-apostas', 'Jogos e apostas', 60),
    ('lazer', 'Lazer', 70),
    ('saude', 'Saúde', 80),
    ('transporte', 'Transporte', 90),
    ('vestuario', 'Vestuário', 100),
    ('outros', 'Outros', 110)
ON DUPLICATE KEY UPDATE
    nome = VALUES(nome),
    ordem = VALUES(ordem),
    ativo = TRUE;
