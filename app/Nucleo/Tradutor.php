<?php

declare(strict_types=1);

namespace Aplicacao\Nucleo;

final class Tradutor
{
    private const IDIOMAS = ['pt-BR', 'en'];

    /** @var array<string, string> */
    private array $mensagens;

    public function __construct(private readonly string $idioma, string $diretorio)
    {
        $arquivo = in_array($idioma, self::IDIOMAS, true) ? $idioma : 'pt-BR';
        $this->mensagens = require $diretorio . '/' . $arquivo . '.php';
    }

    public function obter(string $chave): string
    {
        return $this->mensagens[$chave] ?? $chave;
    }

    public static function idiomaValido(?string $idioma): string
    {
        return in_array($idioma, self::IDIOMAS, true) ? $idioma : 'pt-BR';
    }
}
