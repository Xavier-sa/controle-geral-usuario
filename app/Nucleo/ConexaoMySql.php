<?php

declare(strict_types=1);

namespace Aplicacao\Nucleo;

use PDO;
use RuntimeException;

final class ConexaoMySql
{
    public static function criar(): PDO
    {
        foreach (['DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'] as $chave) {
            if (($valor = getenv($chave)) === false || $valor === '' || str_starts_with($valor, 'PREENCHER_')) {
                throw new RuntimeException("Configuração obrigatória ausente: {$chave}");
            }
        }
        $charset = getenv('DB_CHARSET') ?: 'utf8mb4';
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', getenv('DB_HOST'), getenv('DB_PORT'), getenv('DB_DATABASE'), $charset);
        return new PDO($dsn, (string) getenv('DB_USERNAME'), (string) getenv('DB_PASSWORD'), [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }
}
