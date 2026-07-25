<?php

declare(strict_types=1);

namespace Aplicacao\Nucleo;

use InvalidArgumentException;
use PDO;
use Throwable;

final class RepositorioMySql implements RepositorioDados
{
    /** @var array<string, array{tabela:string,chave:string,colunas:array<int,string>}> */
    private const MAPA = [
        'usuarios.json' => [
            'tabela' => 'usuarios', 'chave' => 'usuario_id',
            'colunas' => ['usuario_id','nome','apelido','email','senha_hash','perfil','status','codigo_indicacao','indicado_por_usuario_id','locale','empresa','renda_mensal','perfil_completo_em','termos_aceitos_em','criado_em','atualizado_em'],
        ],
        'custos_fixos.json' => [
            'tabela' => 'custos_mensais', 'chave' => 'custo_fixo_id',
            'colunas' => ['custo_fixo_id','usuario_id','nome','categoria','icone','valor_mensal','situacao','moeda','ativo','personalizado','recorrente','recorrencia_inicio','recorrencia_fim','dia_vencimento','criado_em','atualizado_em'],
        ],
        'resumo_financeiro.json' => [
            'tabela' => 'resumos_financeiros', 'chave' => 'resumo_id',
            'colunas' => ['resumo_id','usuario_id','periodo','renda_mensal','custos_fixos','saldo_projetado','percentual_meta','moeda','criado_em','atualizado_em'],
        ],
        'fontes_renda.json' => [
            'tabela' => 'fontes_renda', 'chave' => 'fonte_renda_id',
            'colunas' => ['fonte_renda_id','usuario_id','nome','tipo','valor_mensal','ativo','criado_em','atualizado_em'],
        ],
        'convites.json' => [
            'tabela' => 'convites', 'chave' => 'convite_id',
            'colunas' => ['convite_id','indicador_usuario_id','usuario_criado_id','token_hash','codigo_hash','codigo_final','status','expira_em','usado_em','criado_em','atualizado_em'],
        ],
    ];

    public function __construct(private readonly PDO $pdo) {}

    public function listar(string $arquivo): array
    {
        $mapa = $this->mapa($arquivo);
        $colunas = implode(',', array_map(static fn(string $c): string => "`{$c}`", $mapa['colunas']));
        $dados = $this->pdo->query("SELECT {$colunas} FROM `{$mapa['tabela']}` ORDER BY `criado_em`")->fetchAll();
        foreach ($dados as &$registro) {
            foreach (['ativo','personalizado','recorrente'] as $booleano) {
                if (array_key_exists($booleano, $registro)) $registro[$booleano] = (bool) $registro[$booleano];
            }
            foreach (['valor_mensal','renda_mensal','custos_fixos','saldo_projetado'] as $decimal) {
                if (isset($registro[$decimal])) $registro[$decimal] = (float) $registro[$decimal];
            }
            if (isset($registro['percentual_meta'])) $registro['percentual_meta'] = (int) $registro['percentual_meta'];
        }
        unset($registro);
        return $dados;
    }

    public function salvar(string $arquivo, array $dados): void
    {
        $mapa = $this->mapa($arquivo);
        $colunas = $mapa['colunas'];
        $nomes = implode(',', array_map(static fn(string $c): string => "`{$c}`", $colunas));
        $parametros = implode(',', array_map(static fn(string $c): string => ":{$c}", $colunas));
        $atualizacoes = implode(',', array_map(static fn(string $c): string => "`{$c}`=VALUES(`{$c}`)", array_filter($colunas, static fn(string $c): bool => $c !== $mapa['chave'])));
        $sql = "INSERT INTO `{$mapa['tabela']}` ({$nomes}) VALUES ({$parametros}) ON DUPLICATE KEY UPDATE {$atualizacoes}";

        $this->pdo->beginTransaction();
        try {
            $ids = [];
            $salvar = $this->pdo->prepare($sql);
            foreach ($dados as $registro) {
                $valores = [];
                foreach ($colunas as $coluna) $valores[$coluna] = $this->normalizar($coluna, $registro[$coluna] ?? $this->padrao($coluna));
                $salvar->execute($valores);
                $ids[] = (string) $valores[$mapa['chave']];
            }
            if ($ids === []) {
                $this->pdo->exec("DELETE FROM `{$mapa['tabela']}`");
            } else {
                $marcadores = implode(',', array_fill(0, count($ids), '?'));
                $excluir = $this->pdo->prepare("DELETE FROM `{$mapa['tabela']}` WHERE `{$mapa['chave']}` NOT IN ({$marcadores})");
                $excluir->execute($ids);
            }
            $this->pdo->commit();
        } catch (Throwable $erro) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            throw $erro;
        }
    }

    /** @return array{tabela:string,chave:string,colunas:array<int,string>} */
    private function mapa(string $arquivo): array
    {
        return self::MAPA[$arquivo] ?? throw new InvalidArgumentException("Conjunto de dados desconhecido: {$arquivo}");
    }

    private function padrao(string $coluna): mixed
    {
        return match ($coluna) {
            'ativo', 'recorrente' => true,
            'personalizado' => false,
            'recorrencia_inicio' => date('Y-01-01'),
            'valor_mensal', 'renda_mensal', 'custos_fixos', 'saldo_projetado', 'percentual_meta' => 0,
            default => null,
        };
    }

    private function normalizar(string $coluna, mixed $valor): mixed
    {
        if ($valor === null) return null;
        if (in_array($coluna, ['ativo','personalizado','recorrente'], true)) return $valor ? 1 : 0;
        if (str_ends_with($coluna, '_em')) {
            $timestamp = strtotime((string) $valor);
            return $timestamp === false ? null : gmdate('Y-m-d H:i:s', $timestamp);
        }
        return $valor;
    }
}
