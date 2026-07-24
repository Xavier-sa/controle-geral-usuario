<?php

declare(strict_types=1);

namespace Aplicacao\Servicos;

use RuntimeException;

final class ServicoPresenca
{
    private const TEMPO_EXPIRACAO = 60;

    public function __construct(private readonly string $arquivo)
    {
    }

    public function registrar(string $identificadorSessao): int
    {
        $diretorio = dirname($this->arquivo);
        if (!is_dir($diretorio)) {
            mkdir($diretorio, 0775, true);
        }

        $recurso = fopen($this->arquivo, 'c+');
        if ($recurso === false || !flock($recurso, LOCK_EX)) {
            throw new RuntimeException('Não foi possível registrar a presença online.');
        }

        $conteudo = stream_get_contents($recurso);
        $presencas = $conteudo !== false && $conteudo !== '' ? json_decode($conteudo, true) : [];
        $presencas = is_array($presencas) ? $presencas : [];
        $agora = time();

        $presencas = array_filter(
            $presencas,
            static fn (mixed $ultimoAcesso): bool => is_int($ultimoAcesso) && $ultimoAcesso >= $agora - self::TEMPO_EXPIRACAO
        );
        $presencas[hash('sha256', $identificadorSessao)] = $agora;

        rewind($recurso);
        ftruncate($recurso, 0);
        fwrite($recurso, (string) json_encode($presencas, JSON_PRETTY_PRINT));
        fflush($recurso);
        flock($recurso, LOCK_UN);
        fclose($recurso);

        return count($presencas);
    }
}
