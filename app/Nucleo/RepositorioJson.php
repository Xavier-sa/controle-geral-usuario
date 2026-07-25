<?php

declare(strict_types=1);

namespace Aplicacao\Nucleo;

use RuntimeException;

final class RepositorioJson implements RepositorioDados
{
    public function __construct(private readonly string $diretorio)
    {
    }

    /** @return array<int, array<string, mixed>> */
    public function listar(string $arquivo): array
    {
        $conteudo = file_get_contents($this->caminho($arquivo));
        $documento = $conteudo === false ? null : json_decode($conteudo, true);

        if (!is_array($documento) || !isset($documento['data']) || !is_array($documento['data'])) {
            throw new RuntimeException("Arquivo de dados inválido: {$arquivo}");
        }

        return $documento['data'];
    }

    /** @param array<int, array<string, mixed>> $dados */
    public function salvar(string $arquivo, array $dados): void
    {
        $caminho = $this->caminho($arquivo);
        $documento = json_decode((string) file_get_contents($caminho), true);
        $documento['data'] = $dados;
        $json = json_encode($documento, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if ($json === false || file_put_contents($caminho, $json . PHP_EOL, LOCK_EX) === false) {
            throw new RuntimeException("Não foi possível salvar: {$arquivo}");
        }
    }

    private function caminho(string $arquivo): string
    {
        $caminho = $this->diretorio . '/' . basename($arquivo);
        if (!is_file($caminho)) {
            throw new RuntimeException("Arquivo não encontrado: {$arquivo}");
        }
        return $caminho;
    }
}
