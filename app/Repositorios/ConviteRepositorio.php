<?php

declare(strict_types=1);

namespace Aplicacao\Repositorios;

use Aplicacao\Modelos\Convite;
use Aplicacao\Nucleo\RepositorioDados;

final class ConviteRepositorio
{
    private const ARQUIVO = 'convites.json';

    public function __construct(private readonly RepositorioDados $repositorio) {}

    /** @return array{convite:Convite,token:string,codigo:string} */
    public function criar(string $indicadorId): array
    {
        $todos = $this->repositorio->listar(self::ARQUIVO);
        do {
            $token = rtrim(strtr(base64_encode(random_bytes(24)), '+/', '-_'), '=');
            $tokenHash = hash('sha256', $token);
        } while (array_filter($todos, static fn(array $c): bool => hash_equals((string)$c['token_hash'], $tokenHash)));
        do {
            $codigo = $this->codigoCurto();
            $codigoHash = hash('sha256', $codigo);
        } while (array_filter($todos, static fn(array $c): bool => hash_equals((string)$c['codigo_hash'], $codigoHash)));

        $agora = gmdate('c');
        $registro = [
            'convite_id' => bin2hex(random_bytes(16)),
            'indicador_usuario_id' => $indicadorId,
            'usuario_criado_id' => null,
            'token_hash' => $tokenHash,
            'codigo_hash' => $codigoHash,
            'codigo_final' => substr($codigo, -4),
            'status' => 'pendente',
            'expira_em' => gmdate('c', time() + 7 * 86400),
            'usado_em' => null,
            'criado_em' => $agora,
            'atualizado_em' => $agora,
        ];
        $todos[] = $registro;
        $this->repositorio->salvar(self::ARQUIVO, $todos);
        return ['convite'=>new Convite($registro),'token'=>$token,'codigo'=>$codigo];
    }

    /** @return array<int, Convite> */
    public function listarPorIndicador(string $indicadorId): array
    {
        $dados = array_filter($this->repositorio->listar(self::ARQUIVO), static fn(array $c): bool => $c['indicador_usuario_id'] === $indicadorId);
        usort($dados, static fn(array $a,array $b): int => strcmp((string)$b['criado_em'],(string)$a['criado_em']));
        return array_map(static fn(array $dados): Convite => new Convite($dados), $dados);
    }

    public function buscarValido(string $credencial): ?Convite
    {
        $credencial = trim($credencial);
        if ($credencial === '') return null;
        $tokenHash = hash('sha256', $credencial);
        $codigoHash = hash('sha256', strtoupper($credencial));
        foreach ($this->repositorio->listar(self::ARQUIVO) as $dados) {
            if ($dados['status'] !== 'pendente') continue;
            if (strtotime((string)$dados['expira_em']) < time()) continue;
            if (hash_equals((string)$dados['token_hash'], $tokenHash) || hash_equals((string)$dados['codigo_hash'], $codigoHash)) return new Convite($dados);
        }
        return null;
    }

    public function usar(string $conviteId, string $usuarioCriadoId): bool
    {
        $todos = $this->repositorio->listar(self::ARQUIVO);
        foreach ($todos as &$convite) {
            if ($convite['convite_id'] !== $conviteId || $convite['status'] !== 'pendente' || strtotime((string)$convite['expira_em']) < time()) continue;
            $convite['status'] = 'utilizado';
            $convite['usuario_criado_id'] = $usuarioCriadoId;
            $convite['usado_em'] = $convite['atualizado_em'] = gmdate('c');
            $this->repositorio->salvar(self::ARQUIVO, $todos);
            return true;
        }
        unset($convite);
        return false;
    }

    public function revogar(string $conviteId, string $indicadorId): bool
    {
        $todos = $this->repositorio->listar(self::ARQUIVO);
        foreach ($todos as &$convite) {
            if ($convite['convite_id'] !== $conviteId || $convite['indicador_usuario_id'] !== $indicadorId || $convite['status'] !== 'pendente') continue;
            $convite['status'] = 'revogado';
            $convite['atualizado_em'] = gmdate('c');
            $this->repositorio->salvar(self::ARQUIVO, $todos);
            return true;
        }
        unset($convite);
        return false;
    }

    private function codigoCurto(): string
    {
        $alfabeto = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $codigo = '';
        for ($i=0;$i<10;$i++) $codigo .= $alfabeto[random_int(0, strlen($alfabeto)-1)];
        return substr($codigo,0,5).'-'.substr($codigo,5);
    }
}
