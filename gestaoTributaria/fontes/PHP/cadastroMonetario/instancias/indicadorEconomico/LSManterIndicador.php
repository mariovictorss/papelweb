<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
    * Pagina de Formulario de Inclusao/Alteracao de INDICADORES

    * Data de Criacao   : 19/12/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho
    * @ignore

    * $Id: LSManterIndicador.php 63839 2015-10-22 18:08:07Z franver $

    *Casos de uso: uc-05.05.07
*/

/*
$Log$
Revision 1.7  2006/09/15 14:57:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONIndicadorEconomico.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterIndicador";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgFormula = "FMManterFormulaIndicador.php";
$stCaminho   = CAM_GT_MON_INSTANCIAS."indicadorEconomico/";

if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "alterar";
}

switch ($_REQUEST['stAcao']) {
    case 'alterar'  : $pgProx = $pgForm; break;
    case 'excluir'  : $pgProx = $pgProc; break;
    case 'formula'  : $pgProx = $pgFormula; break;
    DEFAULT         : $pgProx = $pgForm;
}

//MANTEM FILTRO E PAGINACAO
$link = Sessao::read( "link" );
$stLink .= "&stAcao=".$_REQUEST['stAcao'];
if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
    $link["pg"]  = $_GET["pg"];
    $link["pos"] = $_GET["pos"];
}

if ( is_array($link) ) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
}

Sessao::write('link'  , $link);
Sessao::write('stLink', $stLink);
//------------------------------------------------------
$obRMONIndicador = new RMONIndicadorEconomico;

//MONTA O FILTRO
if ($_REQUEST['inCodIndicador']) {
    $obRMONIndicador->setCodIndicador( $_REQUEST['inCodIndicador'] );
}if ($_REQUEST['stDescricao']) {
    $obRMONIndicador->setDescricao( $_REQUEST['stDescricao'] );
}if ($_REQUEST['stAbreviatura']) {
    $obRMONIndicador->setAbreviatura( $_REQUEST['stAbreviatura'] );
}if ($_REQUEST['inPrecisao']) {
    $obRMONIndicador->setPrecisao( $_REQUEST['inPrecisao'] );
}

$obRMONIndicador->ListarIndicadores( $rsLista );

$obLista = new Lista;
$obLista->setRecordSet ( $rsLista );

$obLista->setTitulo ('Registros de Indicadores');

//------------------------------------------- CABECALHOS
$obLista->addCabecalho ();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho ();
$obLista->ultimoCabecalho->addConteudo("Código");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho ();
$obLista->ultimoCabecalho->addConteudo("Descrição");
$obLista->ultimoCabecalho->setWidth( 80 );
$obLista->commitCabecalho();

$obLista->addCabecalho ();
$obLista->ultimoCabecalho->addConteudo("Abreviatura ou Símbolo");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho ();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

//-------------------------------------------- DADOS
$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_indicador" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "abreviatura" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();

//-------------------------------------------- ACAO

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $_REQUEST['stAcao'] );
$obLista->ultimaAcao->addCampo("&stDescricao","descricao" );
$obLista->ultimaAcao->addCampo("&inCodIndicador", "cod_indicador" );
$obLista->ultimaAcao->addCampo("&inPrecisao", "precisao" );
$obLista->ultimaAcao->addCampo("&stAbreviatura", "abreviatura" );
$obLista->ultimaAcao->addCampo("&inCodModulo", "cod_modulo" );
$obLista->ultimaAcao->addCampo("&inCodBiblioteca", "cod_biblioteca" );
$obLista->ultimaAcao->addCampo("&inCodFuncao", "cod_funcao" );
$obLista->ultimaAcao->addCampo("&dtVigencia", "inicio_vigencia" );

$obLista->ultimaAcao->addCampo("&stDescQuestao","[cod_indicador]-[descricao]");
if ($_REQUEST['stAcao'] == "excluir") {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}

$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->setAjuda  ( "UC-05.05.07" );
$obFormulario->show();
