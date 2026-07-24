<?php

declare(strict_types=1);

use Aplicacao\Controladores\PainelControlador;
use Aplicacao\Nucleo\Aplicacao;
use Aplicacao\Nucleo\RepositorioJson;
use Aplicacao\Repositorios\CustoFixoRepositorio;
use Aplicacao\Repositorios\ResumoFinanceiroRepositorio;
use Aplicacao\Repositorios\UsuarioRepositorio;
use Aplicacao\Servicos\ServicoPresenca;

$diretorioSessoes = __DIR__ . '/armazenamento/sessoes';
if (!is_dir($diretorioSessoes)) {
    mkdir($diretorioSessoes, 0775, true);
}
session_save_path($diretorioSessoes);
session_start();

require __DIR__ . '/app/Nucleo/Aplicacao.php';

Aplicacao::registrarCarregamentoAutomatico(__DIR__ . '/app');

$repositorioJson = new RepositorioJson(__DIR__ . '/data');
$controlador = new PainelControlador(
    new UsuarioRepositorio($repositorioJson),
    new CustoFixoRepositorio($repositorioJson),
    new ResumoFinanceiroRepositorio($repositorioJson),
    new ServicoPresenca(__DIR__ . '/armazenamento/presencas.json'),
    __DIR__ . '/app/Visoes'
);

$controlador->processar($_SERVER['REQUEST_METHOD'] ?? 'GET', $_GET, $_POST);
