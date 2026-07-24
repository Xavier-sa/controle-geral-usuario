<?php

declare(strict_types=1);

namespace Aplicacao\Repositorios;

use Aplicacao\Modelos\CustoFixo;
use Aplicacao\Nucleo\RepositorioJson;

final class CustoFixoRepositorio
{
    private const CUSTOS_INICIAIS = [
        ['nome' => 'Moradia', 'categoria' => 'habitação', 'icone' => '⌂'],
        ['nome' => 'Energia e água', 'categoria' => 'serviços', 'icone' => '◉'],
        ['nome' => 'Internet', 'categoria' => 'conectividade', 'icone' => '⌁'],
        ['nome' => 'Alimentação', 'categoria' => 'essenciais', 'icone' => '◇'],
        ['nome' => 'Transporte', 'categoria' => 'mobilidade', 'icone' => '→'],
    ];

    public function __construct(private readonly RepositorioJson $repositorio) {}

    /** @return array<int, CustoFixo> */
    public function listarPorUsuario(string $usuarioId): array
    {
        $dados = array_filter($this->repositorio->listar('custos_fixos.json'), static fn(array $c): bool => $c['usuario_id'] === $usuarioId);
        return array_map(static fn(array $d): CustoFixo => new CustoFixo($d), array_values($dados));
    }

    public function criarParaUsuario(string $usuarioId): void
    {
        $todos = $this->repositorio->listar('custos_fixos.json');
        if (array_filter($todos, static fn(array $c): bool => $c['usuario_id'] === $usuarioId)) return;
        foreach (self::CUSTOS_INICIAIS as $modelo) {
            $todos[] = [
                'custo_fixo_id' => bin2hex(random_bytes(16)),
                'usuario_id' => $usuarioId,
                'nome' => $modelo['nome'],
                'categoria' => $modelo['categoria'],
                'icone' => $modelo['icone'],
                'valor_mensal' => 0.0,
                'situacao' => 'pendente',
                'moeda' => 'BRL',
                'ativo' => true,
                'personalizado' => false,
                'criado_em' => gmdate('c'),
                'atualizado_em' => gmdate('c'),
            ];
        }
        $this->repositorio->salvar('custos_fixos.json', $todos);
    }

    public function adicionar(string $usuarioId, string $nome, string $categoria, mixed $valor): bool
    {
        $nome = trim($nome);
        $categoria = trim($categoria);
        $valorNormalizado = $this->normalizarValor($valor);
        if ($nome === '' || strlen($nome) > 80 || $categoria === '' || strlen($categoria) > 50 || $valorNormalizado === false || $valorNormalizado <= 0) {
            return false;
        }

        $todos = $this->repositorio->listar('custos_fixos.json');
        $agora = gmdate('c');
        $todos[] = [
            'custo_fixo_id' => bin2hex(random_bytes(16)),
            'usuario_id' => $usuarioId,
            'nome' => $nome,
            'categoria' => $categoria,
            'icone' => '+',
            'valor_mensal' => round($valorNormalizado, 2),
            'situacao' => 'com_gasto',
            'moeda' => 'BRL',
            'ativo' => true,
            'personalizado' => true,
            'criado_em' => $agora,
            'atualizado_em' => $agora,
        ];
        $this->repositorio->salvar('custos_fixos.json', $todos);
        return true;
    }

    public function excluirPersonalizado(string $usuarioId, string $custoId): bool
    {
        $todos = $this->repositorio->listar('custos_fixos.json');
        $quantidadeAnterior = count($todos);
        $todos = array_values(array_filter($todos, static fn(array $custo): bool =>
            !($custo['usuario_id'] === $usuarioId
                && $custo['custo_fixo_id'] === $custoId
                && ($custo['personalizado'] ?? false) === true)
        ));
        if (count($todos) === $quantidadeAnterior) return false;
        $this->repositorio->salvar('custos_fixos.json', $todos);
        return true;
    }

    public function totalPorUsuario(string $usuarioId): float
    {
        return array_reduce(
            $this->listarPorUsuario($usuarioId),
            static fn(float $total, CustoFixo $custo): float => $total + (float) $custo->obter('valor_mensal'),
            0.0
        );
    }

    /** @param array<string, mixed> $valores @param array<string, mixed> $situacoes */
    public function atualizar(string $usuarioId, array $valores, array $situacoes): float
    {
        $todos = $this->repositorio->listar('custos_fixos.json'); $total = 0.0;
        foreach ($todos as &$custo) {
            if ($custo['usuario_id'] !== $usuarioId) continue;
            $id = (string)$custo['custo_fixo_id']; $situacao = $situacoes[$id] ?? 'pendente';
            $situacao = in_array($situacao, ['com_gasto','sem_gasto'], true) ? $situacao : 'pendente';
            $valor = $this->normalizarValor($valores[$id] ?? 0);
            $custo['situacao'] = $situacao; $custo['valor_mensal'] = $situacao === 'com_gasto' && $valor !== false && $valor > 0 ? round($valor,2) : 0.0;
            if ($situacao === 'com_gasto' && $custo['valor_mensal'] <= 0) $custo['situacao'] = 'pendente';
            $custo['atualizado_em'] = gmdate('c'); $total += (float)$custo['valor_mensal'];
        }
        unset($custo); $this->repositorio->salvar('custos_fixos.json', $todos); return $total;
    }

    public function completo(string $usuarioId): bool
    {
        $custos = $this->listarPorUsuario($usuarioId);
        return $custos !== [] && count(array_filter($custos, static fn(CustoFixo $c): bool => in_array($c->obter('situacao'), ['com_gasto','sem_gasto'], true))) === count($custos);
    }

    public function excluirPorUsuario(string $usuarioId): void
    {
        $todos = array_values(array_filter($this->repositorio->listar('custos_fixos.json'), static fn(array $c): bool => $c['usuario_id'] !== $usuarioId));
        $this->repositorio->salvar('custos_fixos.json', $todos);
    }

    private function normalizarValor(mixed $valor): float|false
    {
        $texto = trim((string) $valor);
        if (str_contains($texto, ',')) {
            $texto = str_replace('.', '', $texto);
            $texto = str_replace(',', '.', $texto);
        }
        return filter_var($texto, FILTER_VALIDATE_FLOAT);
    }
}
