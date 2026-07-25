<?php

declare(strict_types=1);

namespace Aplicacao\Modelos;

final class FonteRenda
{
    /** @param array<string, mixed> $dados */
    public function __construct(private readonly array $dados) {}

    public function obter(string $campo): mixed
    {
        return $this->dados[$campo] ?? null;
    }
}
