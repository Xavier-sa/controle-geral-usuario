# Agente de banco de dados — Prospera

## Missão

Manter o schema MySQL, as migrações e a camada de persistência funcionais,
seguros e compatíveis com a aplicação.

## Regras obrigatórias

1. Nunca versionar `.env`, dumps, senhas, tokens ou dados reais de usuários.
2. Toda alteração estrutural deve gerar um novo arquivo em `database/ddl/`.
   Migrações aplicadas são imutáveis; correções recebem uma nova versão.
3. Dados de referência ficam em `database/dml/` e devem ser idempotentes.
4. Antes de aplicar uma migração, revisar chaves estrangeiras, índices,
   charset `utf8mb4`, impacto de bloqueio e possibilidade de rollback.
5. Nunca executar `DROP`, `TRUNCATE` ou alteração destrutiva sem backup,
   alvo confirmado e autorização explícita.
6. Consultas devem usar prepared statements. Não concatenar entrada do usuário.
7. Renda, empresa, e-mail e relacionamentos de indicação são dados privados.
   Não registrar seus valores em logs nem expô-los em ranking.
8. Após cada mudança, executar:

```bash
php bin/migrar-banco.php
find app bin -name '*.php' -exec php -l {} \;
npm run check
```

9. Validar conexão e escrita dentro de transação com rollback sempre que
   possível. Testes não devem deixar registros artificiais.
10. Manter `data/schema.md`, DDL e código de persistência sincronizados.
11. Toda alteração em `fontes_renda` deve recalcular a renda consolidada do
    usuário e o `saldo_projetado` do resumo financeiro.
12. Recorrências de custos devem aceitar término nulo como permanência,
    validar que o fim não antecede o início e nunca gerar lançamentos duplicados.
13. Convites devem expirar, permitir apenas um uso e persistir somente hashes do
    token e do código. Nunca associar dados pessoais ao convite antes do aceite.

## Fluxo recomendado

1. Inspecionar o schema atual e `schema_migrations`.
2. Criar migração incremental numerada.
3. Validar em ambiente de teste.
4. Fazer backup antes de produção.
5. Aplicar com `php bin/migrar-banco.php`.
6. Conferir tabelas, índices, contagens e logs de erro sem dados pessoais.
