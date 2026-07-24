<?php

declare(strict_types=1);

namespace Aplicacao\Nucleo;

final class Aplicacao
{
    public static function registrarCarregamentoAutomatico(string $diretorioBase): void
    {
        spl_autoload_register(static function (string $classe) use ($diretorioBase): void {
            $prefixo = 'Aplicacao\\';
            if (!str_starts_with($classe, $prefixo)) {
                return;
            }

            $caminho = $diretorioBase . '/' . str_replace('\\', '/', substr($classe, strlen($prefixo))) . '.php';
            if (is_file($caminho)) {
                require $caminho;
            }
        });
    }
}
