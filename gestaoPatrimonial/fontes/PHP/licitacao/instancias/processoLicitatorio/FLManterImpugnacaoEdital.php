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
    * Página de Filtro da Manutenção de Proposta
    * Data de Criação   : 06/10/2006

    * @author Analista: Gelson Wolowski Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @ignore

    * Casos de uso: uc-03.05.27
*/
/*
$Log$
Revision 1.2  2007/06/29 21:13:16  bruce
Bug #9498#

Revision 1.1  2007/06/11 19:01:06  hboaventura
Bug #9146#

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GP_LIC_COMPONENTES."IMontaNumeroLicitacaoMultiploEntidadeUsuario.class.php" );
include_once ( CAM_GA_PROT_COMPONENTES."IPopUpProcesso.class.php" );
include_once ( CAM_GP_COM_COMPONENTES."IPopUpMapaCompras.class.php" );
include_once ( CAM_GP_LIC_COMPONENTES."IPopUpNumeroEdital.class.php" );
include_once ( CAM_GP_LIC_COMPONENTES."ISelectTipoLicitacao.class.php" );
include_once ( CAM_GP_LIC_COMPONENTES."ISelectCriterioJulgamento.class.php" );
include_once ( CAM_GP_COM_COMPONENTES."ISelectTipoObjeto.class.php" );
include_once ( CAM_GP_COM_COMPONENTES."IPopUpObjeto.class.php" );
include_once ( CAM_GP_LIC_COMPONENTES."ISelectComissao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterImpugnacaoEdital";
$pgFilt       = "FL".$stPrograma.".php";
$pgList       = "LS".$stPrograma.".php";
$pgForm       = "FM".$stPrograma.".php";
$pgProc       = "PR".$stPrograma.".php";
$pgOcul       = "OC".$stPrograma.".php";
$pgJS         = "JS".$stPrograma.".js" ;

Sessao::write('link', '');

$stAcao = $request->get('stAcao');
$stCtrl = $request->get('stCtrl');

//DEFINICAO DOS COMPONENTES DO FORMULARIO
$obForm = new Form();
$obForm->setAction ( $pgList  );

$obHdnCtrl = new Hidden();
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl  );

$obHdnAcao = new Hidden();
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obPopUpMapa = new IPopUpMapaCompras($obForm);

$obNumLicitacao = new IMontaNumeroLicitacaoMultiploEntidadeUsuario($obForm);

$obPopUpProcesso = new IPopUpProcesso($obForm);

$obTipoLicitacao = new ISelectTipoLicitacao();

$obCriterioJulgamento = new ISelectCriterioJulgamento();

$obTipoObjeto = new ISelectTipoObjeto();

$obObjeto = new IPopUpObjeto($obForm);

$obEdital = new TextBox;
$obEdital->setRotulo ( 'Número do Edital' );
$obEdital->setInteiro ( true );
$obEdital->setId( "numEdital" );
$obEdital->setName( "numEdital" );

$obComissao = new ISelectComissao();

$obFormulario = new Formulario();
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obNumLicitacao->geraFormulario( $obFormulario );
$obFormulario->addComponente( $obPopUpProcesso );
$obFormulario->addComponente( $obEdital        );
$obFormulario->addComponente( $obPopUpMapa );
$obFormulario->addComponente( $obTipoLicitacao );
$obFormulario->addComponente( $obCriterioJulgamento );
$obFormulario->addComponente( $obTipoObjeto );
$obFormulario->addComponente( $obObjeto );
$obFormulario->addComponente( $obComissao );
$obFormulario->Ok();
$obFormulario->Show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
