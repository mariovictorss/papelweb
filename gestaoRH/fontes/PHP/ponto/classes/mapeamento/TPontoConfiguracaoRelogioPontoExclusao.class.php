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
    * Classe de mapeamento da tabela ponto.configuracao_relogio_ponto_exclusao
    * Data de Criação: 15/10/2008

    * @author Analista     : Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPontoConfiguracaoRelogioPontoExclusao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPontoConfiguracaoRelogioPontoExclusao()
{
    parent::Persistente();
    $this->setTabela("ponto.configuracao_relogio_ponto_exclusao");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_configuracao');

    $this->AddCampo('cod_configuracao'  ,'integer'  ,true  ,'',true,'TPontoConfiguracaoRelogioPonto');
    $this->AddCampo('timestamp_exclusao','timestamp',false ,'',false,false);
    $this->AddCampo('numcgm'            ,'integer'  ,true  ,'',false,'TAdministracaoUsuario');

}
}
?>