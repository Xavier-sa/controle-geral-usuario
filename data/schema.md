# Modelo relacional futuro

Os arquivos JSON usam um envelope de metadados e uma coleção `data`. Cada objeto em `data` representa uma linha futura. IDs são UUIDs, datas seguem ISO 8601 UTC, valores monetários são números decimais e relações usam o sufixo `_id`.

## Tabelas

- `usuarios`: identidade, acesso, apelido público, vínculo de indicação, dados
  financeiros privados do primeiro acesso e aceite registrado dos termos.
- `custos_mensais`: despesas mensais pertencentes a um usuário. O campo booleano
  `personalizado` distingue categorias-base de gastos criados livremente pelo
  usuário. A recorrência possui início, término opcional e dia de vencimento.
- `resumos_financeiros`: consolidação por usuário e período (`YYYY-MM`).
- `fontes_renda`: fontes mensais editáveis de cada usuário; a soma mantém
  `usuarios.renda_mensal` e o saldo projetado sincronizados.
- `categorias_despesa`: catálogo de categorias mantido por DML idempotente.
- `convites`: códigos e links temporários de uso único. Mantém somente os hashes
  das credenciais, o indicador, a expiração e, após o aceite, o usuário criado.
- `schema_migrations`: controle de versão e checksum do DDL/DML aplicado.

Índices implementados: `usuarios(email)` único,
`usuarios(codigo_indicacao)` único, `custos_mensais(usuario_id, ativo)` e
`fontes_renda(usuario_id, ativo)` e `resumos_financeiros(usuario_id, periodo)`
único. Em `convites`, os hashes de token e código são únicos, e há índices por
indicador/status e por expiração. `perfil`, `status` e `situacao` usam enums
controlados. Senhas, tokens e códigos de convite são armazenados somente como
hash; credenciais em texto puro nunca devem ser persistidas.

O ranking deve publicar somente apelido, posição, estrelas e mês de entrada.
E-mail, renda e empresa são privados e não devem ser expostos a outros membros.
