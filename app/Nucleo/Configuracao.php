<?php

declare(strict_types=1);

namespace Aplicacao\Nucleo;

final class Configuracao
{
    public static function carregar(string $arquivo): void
    {
        if (!is_file($arquivo)) return;
        foreach (file($arquivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $linha) {
            $linha = trim($linha);
            if ($linha === '' || str_starts_with($linha, '#') || !str_contains($linha, '=')) continue;
            [$chave, $valor] = explode('=', $linha, 2);
            $chave = trim($chave);
            $valor = trim($valor, " \t\n\r\0\x0B\"'");
            if ($chave !== '' && getenv($chave) === false) {
                putenv("{$chave}={$valor}");
                $_ENV[$chave] = $valor;
            }
        }
    }
}
