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
* Arquivo instância para popup de Dependentes
* Data de Criação: 04/03/2008

* @author Analista: Dagiane Vieria
* @author Desenvolvedor: Alex Cardoso

$Id: FLProcurarCgmServidorDependente.php 59612 2014-09-02 12:00:51Z gelson $

Casos de uso: uc-04.08.17
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_CGM_MAPEAMENTO."TCGM.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarCgmServidorDependente";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//destroi arrays de sessao que armazenam os dados do FILTRO
Sessao::remove( "filtroRelatorio" );
Sessao::remove( "link" );

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );

$obHdnCampoFiltrarPensaoJudicial = new Hidden;
$obHdnCampoFiltrarPensaoJudicial->setName( "boFiltrarPensaoJudicial" );
$obHdnCampoFiltrarPensaoJudicial->setValue( $_GET['boFiltrarPensaoJudicial']  );

$obHdnFiltro = new Hidden;
$obHdnFiltro->setName( "inFiltro" );
$obHdnFiltro->setValue( $_GET['inFiltro'] );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $_GET['stAcao'] );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnForm = new Hidden;
$obHdnForm->setName( "nomForm" );
$obHdnForm->setValue( $_REQUEST["nomForm"] );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST["campoNum"] );

//Define HIDDEN com o o nome do campo texto
$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST["campoNom"] );

//Definição das Caixas de Texto
$obTxtNomeCgm = new TextBox;
$obTxtNomeCgm->setName( "stNomeCgm" );
$obTxtNomeCgm->setRotulo( "Nome" );
$obTxtNomeCgm->setSize( 60 );
$obTxtNomeCgm->setMaxLength( 60 );

//Dados para pessoa fisica
$obTxtCPF = new CPF;
$obTxtCPF->setName( "stCPF" );
$obTxtCPF->setRotulo( "CPF" );
$obTxtCPF->setNull( true );

//Criação do formulário
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnCampoFiltrarPensaoJudicial );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnFiltro );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnForm );
$obFormulario->addHidden( $obHdnCampoNum );
$obFormulario->addHidden( $obHdnCampoNom );
$obFormulario->addTitulo( "Dados para CGM de Servidores com Dependentes" );
$obFormulario->addComponente( $obTxtNomeCgm );
$obFormulario->addComponente( $obTxtCPF );
$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
