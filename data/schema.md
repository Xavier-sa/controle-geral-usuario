# Modelo relacional futuro

Os arquivos JSON usam um envelope de metadados e uma coleção `data`. Cada objeto em `data` representa uma linha futura. IDs são UUIDs, datas seguem ISO 8601 UTC, valores monetários são números decimais e relações usam o sufixo `_id`.

## Tabelas

- `usuarios`: identidade, acesso, apelido público, vínculo de indicação, dados
  financeiros privados do primeiro acesso e aceite registrado dos termos.
- `custos_fixos`: despesas mensais pertencentes a um usuário. O campo booleano
  `personalizado` distingue categorias-base de gastos criados livremente pelo usuário.
- `resumos_financeiros`: consolidação por usuário e período (`YYYY-MM`).

Índices recomendados: `usuarios(email)` único, `usuarios(codigo_indicacao)` único, `custos_fixos(usuario_id, ativo)` e `resumos_financeiros(usuario_id, periodo)` único. O campo `perfil` deve virar enum ou tabela de domínio; `status`, enum `ativo/inativo`; e `situacao`, enum `pendente/com_gasto/sem_gasto`. Senhas são armazenadas somente como hash. Tokens e senhas em texto puro nunca devem ser persistidos.

O ranking deve publicar somente apelido, posição, estrelas e mês de entrada.
E-mail, renda e empresa são privados e não devem ser expostos a outros membros.
