<?php

declare(strict_types=1);

namespace Aplicacao\Controladores;

use Aplicacao\Modelos\Usuario;
use Aplicacao\Nucleo\Tradutor;
use Aplicacao\Nucleo\Visao;
use Aplicacao\Repositorios\CustoFixoRepositorio;
use Aplicacao\Repositorios\FonteRendaRepositorio;
use Aplicacao\Repositorios\ResumoFinanceiroRepositorio;
use Aplicacao\Repositorios\UsuarioRepositorio;
use Aplicacao\Servicos\ServicoPresenca;

final class PainelControlador
{
    private const PAGINAS = ['inicio','custos','calendario','rendas','usuarios','usuario_formulario','indicacao','perfil_inicial'];
    public function __construct(private readonly UsuarioRepositorio $usuarios,private readonly CustoFixoRepositorio $custos,private readonly FonteRendaRepositorio $rendas,private readonly ResumoFinanceiroRepositorio $resumos,private readonly ServicoPresenca $presenca,private readonly string $diretorioVisoes) {}

    /** @param array<string,string> $consulta @param array<string,mixed> $formulario */
    public function processar(string $metodo,array $consulta,array $formulario): void
    {
        $idioma=Tradutor::idiomaValido($consulta['idioma']??$_SESSION['idioma']??null);$_SESSION['idioma']=$idioma;
        $tradutor=new Tradutor($idioma,dirname(__DIR__).'/Idiomas');
        if($metodo==='POST')$this->processarFormulario($formulario,$idioma);
        $usuario=$this->usuarioAtual();
        if(!$usuario){Visao::renderizarSemLayout($this->diretorioVisoes,'login',['idioma'=>$idioma,'tradutor'=>$tradutor,'token'=>$this->token(),'erro'=>$_SESSION['erro_login']??null]);unset($_SESSION['erro_login']);return;}

        $quantidadeOnline=$this->presenca->registrar(session_id());
        if(($consulta['recurso']??'')==='presenca'){header('Content-Type: application/json; charset=utf-8');echo json_encode(['quantidade_online'=>$quantidadeOnline]);return;}
        $pagina=in_array($consulta['pagina']??'inicio',self::PAGINAS,true)?($consulta['pagina']??'inicio'):'inicio';
        if($usuario->obter('perfil_completo_em')===null&&!$usuario->ehAdministrador())$pagina='perfil_inicial';
        if($pagina==='usuario_formulario'&&!$this->usuarios->podeConvidar($usuario)){$pagina='usuarios';}
        $this->custos->criarParaUsuario((string)$usuario->obter('usuario_id'));
        if($pagina==='inicio'&&!$this->custos->completo((string)$usuario->obter('usuario_id'))){header('Location: ?pagina=custos&bloqueado=1');exit;}
        $custos=$this->custos->listarPorUsuario((string)$usuario->obter('usuario_id'));
        $anoCalendario=max(2020,min(2100,(int)($consulta['ano']??date('Y'))));
        Visao::renderizar($this->diretorioVisoes,$pagina,['pagina'=>$pagina,'idioma'=>$idioma,'tradutor'=>$tradutor,'usuarioAtual'=>$usuario,'usuarios'=>$this->usuarios->listar(),'ranking'=>$this->usuarios->ranking(),'podeConvidar'=>$this->usuarios->podeConvidar($usuario),'usuarioEdicao'=>isset($consulta['id'])&&$usuario->ehAdministrador()?$this->usuarios->buscarPorId($consulta['id']):null,'custos'=>$custos,'fontesRenda'=>$this->rendas->listarPorUsuario((string)$usuario->obter('usuario_id')),'resumo'=>$this->resumos->atual((string)$usuario->obter('usuario_id')),'anoCalendario'=>$anoCalendario,'quantidadeOnline'=>$quantidadeOnline,'token'=>$this->token()]);
    }

    /** @param array<string,mixed> $dados */
    private function processarFormulario(array $dados,string $idioma): never
    {
        if(!hash_equals($this->token(),(string)($dados['token']??''))){http_response_code(403);exit('Requisição inválida.');}
        $acao=$dados['acao']??'';
        if($acao==='entrar'){$usuario=$this->usuarios->autenticar((string)($dados['email']??''),(string)($dados['senha']??''));if(!$usuario){$_SESSION['erro_login']='E-mail ou senha inválidos.';header('Location: ?pagina=login');exit;}session_regenerate_id(true);$_SESSION['usuario_id']=$usuario->obter('usuario_id');header('Location: ?pagina=inicio');exit;}
        if($acao==='sair'){session_unset();session_destroy();header('Location: ?pagina=login');exit;}
        $atual=$this->usuarioAtual();if(!$atual){header('Location: ?pagina=login');exit;}
        if($acao==='completar_perfil'){
            $id=(string)$atual->obter('usuario_id');
            $completo=$this->usuarios->completarPerfil($id,$dados);
            if($completo&&$this->rendas->listarPorUsuario($id)===[])$this->rendas->adicionar($id,(string)($dados['empresa']??'Renda principal'),(string)($dados['tipo_renda']??'outros'),$dados['renda_mensal']??0);
            if($completo)$this->sincronizarRenda($id);
            header('Location: ?pagina='.($completo?'custos':'perfil_inicial&erro=1'));exit;
        }
        if($acao==='adicionar_renda'){
            $id=(string)$atual->obter('usuario_id');$ok=$this->rendas->adicionar($id,(string)($dados['nome']??''),(string)($dados['tipo']??''),$dados['valor_mensal']??null);
            if($ok)$this->sincronizarRenda($id);header('Location: ?pagina=rendas&'.($ok?'adicionada=1':'erro=1'));exit;
        }
        if($acao==='atualizar_renda'){
            $id=(string)$atual->obter('usuario_id');$ok=$this->rendas->atualizar($id,(string)($dados['fonte_renda_id']??''),(string)($dados['nome']??''),(string)($dados['tipo']??''),$dados['valor_mensal']??null);
            if($ok)$this->sincronizarRenda($id);header('Location: ?pagina=rendas&'.($ok?'salva=1':'erro=1'));exit;
        }
        if($acao==='excluir_renda'){
            $id=(string)$atual->obter('usuario_id');$ok=count($this->rendas->listarPorUsuario($id))>1&&$this->rendas->excluir($id,(string)($dados['fonte_renda_id']??''));
            if($ok)$this->sincronizarRenda($id);header('Location: ?pagina=rendas&'.($ok?'excluida=1':'ultima=1'));exit;
        }
        if($acao==='adicionar_custo'){
            $id=(string)$atual->obter('usuario_id');
            $nome=(string)($dados['nome']??'');
            if($nome==='outro')$nome=(string)($dados['nome_outro']??'');
            $adicionado=$this->custos->adicionar($id,$nome,(string)($dados['categoria']??''),$dados['valor']??null);
            if($adicionado)$this->resumos->atualizarCustos($id,$this->custos->totalPorUsuario($id));
            header('Location: ?pagina=custos&'.($adicionado?'adicionado=1':'erro_custo=1'));exit;
        }
        if($acao==='excluir_custo'){
            $id=(string)$atual->obter('usuario_id');
            $excluido=$this->custos->excluirPersonalizado($id,(string)($dados['custo_id']??''));
            if($excluido)$this->resumos->atualizarCustos($id,$this->custos->totalPorUsuario($id));
            header('Location: ?pagina=custos&'.($excluido?'excluido=1':'erro_custo=1'));exit;
        }
        if($acao==='salvar_recorrencia'){
            $id=(string)$atual->obter('usuario_id');$ok=$this->custos->atualizarRecorrencia($id,(string)($dados['custo_id']??''),isset($dados['recorrente']),(string)($dados['inicio']??''),isset($dados['fim'])?(string)$dados['fim']:null,$dados['dia_vencimento']??null);
            $ano=max(2020,min(2100,(int)($dados['ano']??date('Y'))));header('Location: ?pagina=calendario&ano='.$ano.'&'.($ok?'salvo=1':'erro=1'));exit;
        }
        if($acao==='salvar_custos'&&is_array($dados['situacoes']??null)){$id=(string)$atual->obter('usuario_id');$valores=is_array($dados['valores']??null)?$dados['valores']:[];$total=$this->custos->atualizar($id,$valores,$dados['situacoes']);$this->resumos->atualizarCustos($id,$total);header('Location: ?pagina=custos&salvo=1');exit;}
        if($acao==='salvar_usuario'&&$this->usuarios->podeConvidar($atual)){$editando=$atual->ehAdministrador()&&!empty($dados['usuario_id']);$idEdicao=$editando?(string)$dados['usuario_id']:null;if($this->usuarios->emailEmUso((string)($dados['email']??''),$idEdicao)){header('Location: ?pagina=usuario_formulario&erro=email'.($idEdicao?'&id='.rawurlencode($idEdicao):''));exit;}$dados['perfil']=$editando?($dados['perfil']??'membro'):'membro';$dados['status']=$editando?($dados['status']??'ativo'):'ativo';$usuario=$this->usuarios->salvar($dados,$idEdicao,$editando?null:(string)$atual->obter('usuario_id'));if(!$editando)$this->custos->criarParaUsuario((string)$usuario->obter('usuario_id'));header('Location: ?pagina=usuarios&convidado=1');exit;}
        if($atual->ehAdministrador()&&$acao==='excluir_usuario'&&!empty($dados['usuario_id'])&&$dados['usuario_id']!==$atual->obter('usuario_id')){$this->usuarios->excluir((string)$dados['usuario_id']);$this->custos->excluirPorUsuario((string)$dados['usuario_id']);header('Location: ?pagina=usuarios');exit;}
        header('Location: ?pagina=inicio&idioma='.rawurlencode($idioma));exit;
    }
    private function usuarioAtual():?Usuario{return isset($_SESSION['usuario_id'])?$this->usuarios->buscarPorId((string)$_SESSION['usuario_id']):null;}
    private function token():string{return $_SESSION['token']??=bin2hex(random_bytes(24));}
    private function normalizarValor(string $valor):float{$texto=trim($valor);if(str_contains($texto,','))$texto=str_replace(',','.',str_replace('.','',$texto));return (float)$texto;}
    private function sincronizarRenda(string $usuarioId):void{$total=$this->rendas->totalPorUsuario($usuarioId);$this->usuarios->atualizarRendaTotal($usuarioId,$total);$this->resumos->atualizarRenda($usuarioId,$total);}
}
