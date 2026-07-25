<?php

declare(strict_types=1);

namespace Aplicacao\Repositorios;

use Aplicacao\Modelos\FonteRenda;
use Aplicacao\Nucleo\RepositorioDados;

final class FonteRendaRepositorio
{
    public function __construct(private readonly RepositorioDados $repositorio) {}

    /** @return array<int, FonteRenda> */
    public function listarPorUsuario(string $usuarioId): array
    {
        $dados = array_filter(
            $this->repositorio->listar('fontes_renda.json'),
            static fn(array $fonte): bool => $fonte['usuario_id'] === $usuarioId && ($fonte['ativo'] ?? true)
        );
        return array_map(static fn(array $dados): FonteRenda => new FonteRenda($dados), array_values($dados));
    }

    public function adicionar(string $usuarioId, string $nome, string $tipo, mixed $valor): bool
    {
        $nome = trim($nome);
        $valor = $this->normalizarValor($valor);
        if ($nome === '' || strlen($nome) > 80 || $valor === false || $valor <= 0) return false;
        $tipo = in_array($tipo, ['salario','aplicativo','autonomo','bico','beneficio','outros'], true) ? $tipo : 'outros';
        $agora = gmdate('c');
        $todos = $this->repositorio->listar('fontes_renda.json');
        $todos[] = [
            'fonte_renda_id' => bin2hex(random_bytes(16)),
            'usuario_id' => $usuarioId,
            'nome' => $nome,
            'tipo' => $tipo,
            'valor_mensal' => round($valor, 2),
            'ativo' => true,
            'criado_em' => $agora,
            'atualizado_em' => $agora,
        ];
        $this->repositorio->salvar('fontes_renda.json', $todos);
        return true;
    }

    public function atualizar(string $usuarioId, string $fonteId, string $nome, string $tipo, mixed $valor): bool
    {
        $nome = trim($nome);
        $valor = $this->normalizarValor($valor);
        if ($nome === '' || strlen($nome) > 80 || $valor === false || $valor <= 0) return false;
        $tipo = in_array($tipo, ['salario','aplicativo','autonomo','bico','beneficio','outros'], true) ? $tipo : 'outros';
        $todos = $this->repositorio->listar('fontes_renda.json');
        foreach ($todos as &$fonte) {
            if ($fonte['fonte_renda_id'] !== $fonteId || $fonte['usuario_id'] !== $usuarioId) continue;
            $fonte['nome'] = $nome;
            $fonte['tipo'] = $tipo;
            $fonte['valor_mensal'] = round($valor, 2);
            $fonte['atualizado_em'] = gmdate('c');
            $this->repositorio->salvar('fontes_renda.json', $todos);
            return true;
        }
        unset($fonte);
        return false;
    }

    public function excluir(string $usuarioId, string $fonteId): bool
    {
        $todos = $this->repositorio->listar('fontes_renda.json');
        $anteriores = count($todos);
        $todos = array_values(array_filter($todos, static fn(array $fonte): bool =>
            !($fonte['fonte_renda_id'] === $fonteId && $fonte['usuario_id'] === $usuarioId)
        ));
        if (count($todos) === $anteriores) return false;
        $this->repositorio->salvar('fontes_renda.json', $todos);
        return true;
    }

    public function totalPorUsuario(string $usuarioId): float
    {
        return array_reduce(
            $this->listarPorUsuario($usuarioId),
            static fn(float $total, FonteRenda $fonte): float => $total + (float) $fonte->obter('valor_mensal'),
            0.0
        );
    }

    private function normalizarValor(mixed $valor): float|false
    {
        $texto = trim((string) $valor);
        if (str_contains($texto, ',')) $texto = str_replace(',', '.', str_replace('.', '', $texto));
        return filter_var($texto, FILTER_VALIDATE_FLOAT);
    }
}
