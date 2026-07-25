<?php

declare(strict_types=1);

namespace Aplicacao\Nucleo;

interface RepositorioDados
{
    /** @return array<int, array<string, mixed>> */
    public function listar(string $arquivo): array;

    /** @param array<int, array<string, mixed>> $dados */
    public function salvar(string $arquivo, array $dados): void;
}
