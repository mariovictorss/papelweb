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
/*
 * Processamento de Configurar Responsavel Tecnico TCEPE
 * Data de Criação: 17/10/2014

 * @author Desenvolvedor Evandro Melos

 * @package URBEM
 * @subpackage

 * @ignore

 * $Id: PRManterTecnicoResponsavel.php 60414 2014-10-20 12:15:20Z evandro $
 
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TCEPE_MAPEAMENTO.'TTCEPEResponsavelTecnico.class.php';

$stPrograma = "ManterTecnicoResponsavel";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obTTCEPEResponsavelTecnico = new TTCEPEResponsavelTecnico();

//Inicia o controle de transação
Sessao::setTrataExcecao(true);
Sessao::getTransacao()->setMapeamento( $obTTCEPEResponsavelTecnico );

//Exclui todos os registros da tabela e insere novamente.
$obTTCEPEResponsavelTecnico->excluirTodos();

$arTecnicoResponsavel = Sessao::read('arTecnicoResponsavel');

//Percorre o Array de dados de Responsavel Tecnico e insere.
foreach ($arTecnicoResponsavel as $arResponsavel) {
    $obTTCEPEResponsavelTecnico->setDado('exercicio'        , Sessao::getExercicio()            );
    $obTTCEPEResponsavelTecnico->setDado('cgm_responsavel'  , $arResponsavel['cgm_responsavel'] );
    $obTTCEPEResponsavelTecnico->setDado('cod_entidade'     , $arResponsavel['cod_entidade']    );
    $obTTCEPEResponsavelTecnico->setDado('cod_tipo'         , $arResponsavel['cod_tipo']        );
    $obTTCEPEResponsavelTecnico->setDado('crc'              , $arResponsavel['crc']             );
    $obTTCEPEResponsavelTecnico->setDado('dt_inicio'        , $arResponsavel['dt_inicio']      );
    $obTTCEPEResponsavelTecnico->setDado('dt_fim'           , $arResponsavel['dt_fim']        );
    $obTTCEPEResponsavelTecnico->inclusao();
}

//Encerra o controle de transação
Sessao::encerraExcecao();

SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao,"Configuração ","incluir","incluir_n", Sessao::getId(), "../");

?>