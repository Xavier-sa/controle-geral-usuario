<?php

declare(strict_types=1);

namespace Aplicacao\Repositorios;

use Aplicacao\Modelos\Usuario;
use Aplicacao\Nucleo\RepositorioJson;

final class UsuarioRepositorio
{
    private const ARQUIVO = 'usuarios.json';

    public function __construct(private readonly RepositorioJson $repositorio) {}

    /** @return array<int, Usuario> */
    public function listar(): array
    {
        return array_map(static fn (array $dados): Usuario => new Usuario($dados), $this->repositorio->listar(self::ARQUIVO));
    }

    public function buscarPorId(string $identificador): ?Usuario
    {
        foreach ($this->listar() as $usuario) if ($usuario->obter('usuario_id') === $identificador) return $usuario;
        return null;
    }

    public function autenticar(string $email, string $senha): ?Usuario
    {
        foreach ($this->listar() as $usuario) {
            if (strtolower((string) $usuario->obter('email')) === strtolower(trim($email))
                && $usuario->obter('status') === 'ativo'
                && password_verify($senha, (string) $usuario->obter('senha_hash'))) return $usuario;
        }
        return null;
    }

    public function emailEmUso(string $email, ?string $ignorarUsuarioId = null): bool
    {
        $email = strtolower(trim($email));
        foreach ($this->listar() as $usuario) {
            if ($usuario->obter('usuario_id') !== $ignorarUsuarioId
                && strtolower((string) $usuario->obter('email')) === $email) return true;
        }
        return false;
    }

    /** @param array<string, mixed> $dados */
    public function salvar(array $dados, ?string $identificador = null, ?string $indicadorId = null): Usuario
    {
        $usuarios = $this->repositorio->listar(self::ARQUIVO);
        $agora = gmdate('c');
        $registro = null;

        foreach ($usuarios as $indice => $usuario) {
            if ($usuario['usuario_id'] !== $identificador) continue;
            $registro = array_merge($usuario, $this->dadosPermitidos($dados));
            if (!empty($dados['senha'])) $registro['senha_hash'] = password_hash((string) $dados['senha'], PASSWORD_DEFAULT);
            $registro['atualizado_em'] = $agora;
            $usuarios[$indice] = $registro;
        }

        if ($registro === null) {
            $registro = array_merge($this->dadosPermitidos($dados), [
                'usuario_id' => $this->uuid(), 'senha_hash' => password_hash((string) $dados['senha'], PASSWORD_DEFAULT),
                'codigo_indicacao' => strtoupper(substr(preg_replace('/[^a-z0-9]/i', '', (string) $dados['nome']), 0, 10)) . random_int(100, 999),
                'apelido' => trim((string) ($dados['apelido'] ?? $dados['nome'] ?? '')),
                'indicado_por_usuario_id' => $indicadorId, 'locale' => 'pt-BR', 'termos_aceitos_em' => null,
                'empresa' => null, 'renda_mensal' => null, 'perfil_completo_em' => null,
                'criado_em' => $agora, 'atualizado_em' => $agora
            ]);
            $usuarios[] = $registro;
        }

        $this->repositorio->salvar(self::ARQUIVO, $usuarios);
        return new Usuario($registro);
    }

    /** @param array<string, mixed> $dados */
    public function completarPerfil(string $identificador, array $dados): bool
    {
        $empresa = trim((string) ($dados['empresa'] ?? ''));
        $renda = $this->normalizarValor($dados['renda_mensal'] ?? null);
        if ($empresa === '' || strlen($empresa) > 100 || $renda === false || $renda < 0 || empty($dados['aceite_privacidade'])) return false;

        $usuarios = $this->repositorio->listar(self::ARQUIVO);
        foreach ($usuarios as &$usuario) {
            if ($usuario['usuario_id'] !== $identificador) continue;
            $agora = gmdate('c');
            $usuario['empresa'] = $empresa;
            $usuario['renda_mensal'] = round($renda, 2);
            $usuario['perfil_completo_em'] = $agora;
            $usuario['termos_aceitos_em'] = $agora;
            $usuario['atualizado_em'] = $agora;
            $this->repositorio->salvar(self::ARQUIVO, $usuarios);
            return true;
        }
        unset($usuario);
        return false;
    }

    public function podeConvidar(Usuario $usuario): bool
    {
        return $usuario->ehAdministrador()
            || ($usuario->obter('perfil_completo_em') !== null
                && $usuario->obter('empresa') !== null
                && $usuario->obter('renda_mensal') !== null);
    }

    /** @return array<int, array{usuario: Usuario, estrelas: int, posicao: int}> */
    public function ranking(): array
    {
        $usuarios = $this->listar();
        $linhas = [];
        foreach ($usuarios as $usuario) {
            if ($usuario->obter('status') !== 'ativo') continue;
            $estrelas = count(array_filter($usuarios, static fn(Usuario $convidado): bool =>
                $convidado->obter('indicado_por_usuario_id') === $usuario->obter('usuario_id')
                && $convidado->obter('perfil_completo_em') !== null
            ));
            $linhas[] = ['usuario' => $usuario, 'estrelas' => $estrelas, 'posicao' => 0];
        }
        usort($linhas, static fn(array $a, array $b): int =>
            $b['estrelas'] <=> $a['estrelas']
            ?: strcmp((string) $a['usuario']->obter('criado_em'), (string) $b['usuario']->obter('criado_em'))
        );
        foreach ($linhas as $indice => &$linha) $linha['posicao'] = $indice + 1;
        unset($linha);
        return $linhas;
    }

    public function excluir(string $identificador): void
    {
        $usuarios = array_values(array_filter($this->repositorio->listar(self::ARQUIVO), static fn (array $u): bool => $u['usuario_id'] !== $identificador));
        $this->repositorio->salvar(self::ARQUIVO, $usuarios);
    }

    /** @param array<string, mixed> $dados @return array<string, string> */
    private function dadosPermitidos(array $dados): array
    {
        return ['nome'=>trim((string)($dados['nome']??'')),'apelido'=>trim((string)($dados['apelido']??$dados['nome']??'')),'email'=>strtolower(trim((string)($dados['email']??''))),
            'perfil'=>in_array($dados['perfil']??'', ['administrador','membro'], true)?$dados['perfil']:'membro',
            'status'=>in_array($dados['status']??'', ['ativo','inativo'], true)?$dados['status']:'ativo'];
    }

    private function uuid(): string
    {
        $d = random_bytes(16); $d[6] = chr((ord($d[6]) & 0x0f) | 0x40); $d[8] = chr((ord($d[8]) & 0x3f) | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($d), 4));
    }

    private function normalizarValor(mixed $valor): float|false
    {
        $texto = trim((string) $valor);
        if (str_contains($texto, ',')) $texto = str_replace(',', '.', str_replace('.', '', $texto));
        return filter_var($texto, FILTER_VALIDATE_FLOAT);
    }
}
