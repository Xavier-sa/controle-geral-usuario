<?php

declare(strict_types=1);

namespace Aplicacao\Repositorios;

use Aplicacao\Modelos\ResumoFinanceiro;
use Aplicacao\Nucleo\RepositorioDados;

final class ResumoFinanceiroRepositorio
{
    public function __construct(private readonly RepositorioDados $repositorio)
    {
    }

    public function atual(string $usuarioId): ResumoFinanceiro
    {
        foreach ($this->repositorio->listar('resumo_financeiro.json') as $resumo) if ($resumo['usuario_id'] === $usuarioId) return new ResumoFinanceiro($resumo);
        return new ResumoFinanceiro(['usuario_id'=>$usuarioId,'renda_mensal'=>0.0,'custos_fixos'=>0.0,'saldo_projetado'=>0.0,'percentual_meta'=>0]);
    }

    public function atualizarCustos(string $usuarioId, float $totalCustos): void
    {
        $resumos = $this->repositorio->listar('resumo_financeiro.json');
        if ($resumos === []) {
            return;
        }

        foreach ($resumos as &$resumo) {
            if ($resumo['usuario_id'] !== $usuarioId) continue;
            $resumo['custos_fixos']=$totalCustos;$resumo['saldo_projetado']=(float)$resumo['renda_mensal']-$totalCustos;$resumo['atualizado_em']=gmdate('c');$encontrado=true;
        }
        unset($resumo);
        if (!isset($encontrado)) $resumos[]=['resumo_id'=>bin2hex(random_bytes(16)),'usuario_id'=>$usuarioId,'periodo'=>date('Y-m'),'renda_mensal'=>0.0,'custos_fixos'=>$totalCustos,'saldo_projetado'=>-$totalCustos,'percentual_meta'=>0,'moeda'=>'BRL','criado_em'=>gmdate('c'),'atualizado_em'=>gmdate('c')];
        $this->repositorio->salvar('resumo_financeiro.json', $resumos);
    }

    public function atualizarRenda(string $usuarioId, float $renda): void
    {
        $resumos = $this->repositorio->listar('resumo_financeiro.json');
        foreach ($resumos as &$resumo) {
            if ($resumo['usuario_id'] !== $usuarioId) continue;
            $resumo['renda_mensal'] = $renda;
            $resumo['saldo_projetado'] = $renda - (float) $resumo['custos_fixos'];
            $resumo['atualizado_em'] = gmdate('c');
            $this->repositorio->salvar('resumo_financeiro.json', $resumos);
            return;
        }
        unset($resumo);
        $resumos[] = ['resumo_id'=>bin2hex(random_bytes(16)),'usuario_id'=>$usuarioId,'periodo'=>date('Y-m'),'renda_mensal'=>$renda,'custos_fixos'=>0.0,'saldo_projetado'=>$renda,'percentual_meta'=>0,'moeda'=>'BRL','criado_em'=>gmdate('c'),'atualizado_em'=>gmdate('c')];
        $this->repositorio->salvar('resumo_financeiro.json', $resumos);
    }
}
