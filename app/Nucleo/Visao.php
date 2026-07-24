<?php

declare(strict_types=1);

namespace Aplicacao\Nucleo;

final class Visao
{
    /** @param array<string, mixed> $dados */
    public static function renderizar(string $diretorio, string $arquivo, array $dados): void
    {
        if (!class_exists('Visao', false)) {
            class_alias(self::class, 'Visao');
        }
        extract($dados, EXTR_SKIP);
        ob_start();
        require $diretorio . '/paginas/' . $arquivo . '.php';
        $conteudo = (string) ob_get_clean();
        require $diretorio . '/layout/principal.php';
    }

    public static function escapar(mixed $valor): string
    {
        return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
    }

    /** @param array<string, mixed> $dados */
    public static function renderizarSemLayout(string $diretorio, string $arquivo, array $dados): void
    {
        if (!class_exists('Visao', false)) class_alias(self::class, 'Visao');
        extract($dados, EXTR_SKIP);
        require $diretorio . '/paginas/' . $arquivo . '.php';
    }

    public static function moeda(float $valor, string $idioma): string
    {
        $numero = number_format($valor, 2, $idioma === 'pt-BR' ? ',' : '.', $idioma === 'pt-BR' ? '.' : ',');
        return $idioma === 'pt-BR' ? "R$ {$numero}" : "BRL {$numero}";
    }
}
