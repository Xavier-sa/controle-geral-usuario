<?php

declare(strict_types=1);

namespace Aplicacao\Modelos;

final class Usuario
{
    /** @param array<string, mixed> $dados */
    public function __construct(private array $dados)
    {
    }

    public function obter(string $campo): mixed
    {
        return $this->dados[$campo] ?? null;
    }

    /** @return array<string, mixed> */
    public function paraArray(): array
    {
        return $this->dados;
    }

    public function ehAdministrador(): bool
    {
        return $this->obter('perfil') === 'administrador';
    }
}
