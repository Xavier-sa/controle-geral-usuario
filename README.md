<p align="center">
  <img src="assets/images/feijao-prosperidade.svg" alt="Prospera: capa com uma prévia do painel financeiro" width="100%">
</p>

# Prospera

Painel financeiro em PHP 8.2 que ajuda você a registrar custos, acompanhar
metas e construir patrimônio. O projeto usa arquitetura MVC e, nesta versão,
armazena os dados em JSON.

Além das categorias-base, cada pessoa pode adicionar despesas cotidianas
personalizadas — como café da manhã, lazer e hábitos pessoais — para aproximar
o saldo projetado da sua realidade.

<p align="center">
  <img src="assets/images/fluxo-prospera.svg" alt="Jornada no Prospera: registre custos, acompanhe o progresso e construa patrimônio" width="100%">
</p>

## Executar

```bash
cp data/usuarios.example.json data/usuarios.json
cp data/custos_fixos.example.json data/custos_fixos.json
cp data/resumo_financeiro.example.json data/resumo_financeiro.json
php -S localhost:8000
```

Acesse `http://localhost:8000`. Não há dependências ou etapa de compilação.

## Acesso inicial

Crie o administrador inicial diretamente no armazenamento seguro do ambiente.
Nunca publique credenciais padrão ou dados reais no repositório.

O administrador pode criar, editar e excluir usuários. Novos usuários recebem
as categorias de custos como pendentes e só acessam a área de prosperidade
depois de informar um valor ou selecionar “Não tenho gasto aqui” em todas elas.

O acesso é fechado por convite. Xavier é o administrador raiz; membros com o
perfil financeiro concluído também podem convidar pessoas. Um convite concluído
gera uma estrela para o indicador e alimenta o ranking público por apelido.
E-mail, renda e empresa não aparecem para outros membros.

## Arquitetura

```text
index.php                 ponto único de entrada
app/Controladores/        recebe a requisição e coordena o fluxo
app/Modelos/              entidades do domínio
app/Repositorios/         leitura e gravação dos dados
app/Nucleo/               infraestrutura comum, tradução e views
app/Visoes/layout/        estrutura visual compartilhada
app/Visoes/componentes/   trechos reutilizáveis
app/Visoes/paginas/       conteúdo de cada página
app/Idiomas/              textos em português e inglês
data/                     persistência JSON provisória
css/ e js/                comportamento e apresentação do navegador
```

O controlador não acessa arquivos diretamente. Ele usa repositórios, permitindo
substituir JSON por repositórios SQL no futuro. As views não alteram dados. O
JavaScript cuida somente do menu móvel, aceite local dos termos e cópia do
convite.

## Dados e segurança

Os JSONs usam UUIDs, chaves estrangeiras e datas ISO 8601. Consulte
`data/schema.md`. A aplicação utiliza autenticação por senha com hash,
autorização por perfil e token CSRF. Antes da publicação, substitua os JSONs
por um banco de dados seguro e configure backups, auditoria e rotação de acesso.

Os arquivos `data/*.json` reais não são versionados porque armazenam dados
pessoais e financeiros. Somente os modelos anônimos `*.example.json` fazem
parte do repositório.

Para testar novamente o primeiro acesso, remova `prospera_termos_versao` do
armazenamento local do navegador.

## Validação

```bash
npm run check
find app -name '*.php' -exec php -l {} \;
```

## Suporte

xavier@wasxtech.com.br

Consulte também [CONTRIBUTING.md](CONTRIBUTING.md).
