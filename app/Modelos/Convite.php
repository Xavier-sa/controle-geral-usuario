<?php

declare(strict_types=1);

namespace Aplicacao\Modelos;

final class Convite
{
    /** @param array<string, mixed> $dados */
    public function __construct(private readonly array $dados) {}

    public function obter(string $campo): mixed
    {
        return $this->dados[$campo] ?? null;
    }

    public function expirado(): bool
    {
        return strtotime((string)$this->obter('expira_em')) < time();
    }
}
