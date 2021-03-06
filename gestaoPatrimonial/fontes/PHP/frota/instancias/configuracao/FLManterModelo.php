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
    * Data de Criação: 16/11/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Id: FLManterModelo.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.02.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_FRO_COMPONENTES."ISelectMarcaVeiculo.class.php" );
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaMarca.class.php" );

$stPrograma = "ManterModelo";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Remove o filtro da sessão
Sessao::remove('filtro');

//cria um novo formulario
$obForm = new Form;
$obForm->setAction ($pgList);

//Cria o hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

//cria a acao de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl" );
$obHdnCtrl->setValue("");

//recupera as marcas do sistema
$obTFrotaMarca = new TFrotaMarca();
$obTFrotaMarca->recuperaTodos( $rsMarca );

//cria um select para as marcas
$obSlMarca = new ISelectMarcaVeiculo( $obForm );
$obSlMarca->setNull( true );

//cria textbox para o modelo
$obTxtModelo = new TextBox();
$obTxtModelo->setName( 'stModelo' );
$obTxtModelo->setId( 'stModelo' );
$obTxtModelo->setRotulo( 'Descrição' );
$obTxtModelo->setTitle( 'Informe a descrição do modelo.' );
$obTxtModelo->setMaxLength( 30 );
$obTxtModelo->setSize( 30 );

$obTipoBusca = new TipoBusca( $obTxtModelo );

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->setAjuda('uc-03.02.04');
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );
$obFormulario->addTitulo    ( "Dados para o Filtro" );
$obFormulario->addComponente( $obSlMarca );
$obFormulario->addComponente( $obTipoBusca );
$obFormulario->OK();
$obFormulario->show();
