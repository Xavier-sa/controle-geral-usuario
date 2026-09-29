<?php

declare(strict_types=1);

namespace Aplicacao\Modelos;

use InvalidArgumentException;

final class CustoFixo
{
    /** @param array<string, mixed> $dados */
    public function __construct(private readonly array $dados)
    {
    }

    public function obter(string $campo): mixed
    {
        return $this->dados[$campo] ?? null;
    }

    public function pertenceAoPeriodo(string $periodo): bool
    {
        if (!preg_match('/^\\d{4}-(0[1-9]|1[0-2])$/', $periodo)) {
            throw new InvalidArgumentException('Período inválido. Use o formato YYYY-MM.');
        }
        $inicio = substr((string) ($this->obter('recorrencia_inicio') ?: date('Y-01-01')), 0, 7);
        $fimValor = $this->obter('recorrencia_fim');
        $fim = $fimValor ? substr((string) $fimValor, 0, 7) : null;
        $recorrente = $this->obter('recorrente') === null ? true : (bool) $this->obter('recorrente');
        if (!$recorrente) return $periodo === $inicio;
        return $periodo >= $inicio && ($fim === null || $periodo <= $fim);
    }
}
