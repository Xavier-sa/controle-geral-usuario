<?php

declare(strict_types=1);

use Aplicacao\Nucleo\Aplicacao;
use Aplicacao\Nucleo\Configuracao;
use Aplicacao\Nucleo\ConexaoMySql;

require dirname(__DIR__) . '/app/Nucleo/Aplicacao.php';
Aplicacao::registrarCarregamentoAutomatico(dirname(__DIR__) . '/app');
Configuracao::carregar(dirname(__DIR__) . '/.env');
$pdo = ConexaoMySql::criar();

$arquivos = array_merge(
    glob(dirname(__DIR__) . '/database/ddl/*.sql') ?: [],
    glob(dirname(__DIR__) . '/database/dml/*.sql') ?: []
);
sort($arquivos);

foreach ($arquivos as $arquivo) {
    $versao = basename(dirname($arquivo)) . '/' . basename($arquivo);
    $sql = (string) file_get_contents($arquivo);
    $checksum = hash('sha256', $sql);
    try {
        $consulta = $pdo->prepare('SELECT checksum FROM schema_migrations WHERE versao = ?');
        $consulta->execute([$versao]);
        $existente = $consulta->fetchColumn();
    } catch (Throwable) {
        $existente = false;
    }
    if ($existente === $checksum) {
        echo "OK (já aplicada): {$versao}\n";
        continue;
    }
    if ($existente !== false) throw new RuntimeException("Migração alterada após aplicação: {$versao}");

    $pdo->exec($sql);
    $registro = $pdo->prepare('INSERT INTO schema_migrations (versao, checksum) VALUES (?, ?)');
    $registro->execute([$versao, $checksum]);
    echo "APLICADA: {$versao}\n";
}
