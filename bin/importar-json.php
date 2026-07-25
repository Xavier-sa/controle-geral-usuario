<?php

declare(strict_types=1);

use Aplicacao\Nucleo\Aplicacao;
use Aplicacao\Nucleo\Configuracao;
use Aplicacao\Nucleo\ConexaoMySql;
use Aplicacao\Nucleo\RepositorioJson;
use Aplicacao\Nucleo\RepositorioMySql;

if (!in_array('--confirmar', $argv, true)) {
    fwrite(STDERR, "Importação não executada. Use --confirmar para substituir os dados MySQL pelos JSONs locais.\n");
    exit(2);
}

require dirname(__DIR__) . '/app/Nucleo/Aplicacao.php';
Aplicacao::registrarCarregamentoAutomatico(dirname(__DIR__) . '/app');
Configuracao::carregar(dirname(__DIR__) . '/.env');
$origem = new RepositorioJson(dirname(__DIR__) . '/data');
$destino = new RepositorioMySql(ConexaoMySql::criar());

foreach (['usuarios.json','custos_fixos.json','resumo_financeiro.json','fontes_renda.json'] as $arquivo) {
    $dados = $origem->listar($arquivo);
    $destino->salvar($arquivo, $dados);
    echo basename($arquivo) . ': ' . count($dados) . " registros importados\n";
}
