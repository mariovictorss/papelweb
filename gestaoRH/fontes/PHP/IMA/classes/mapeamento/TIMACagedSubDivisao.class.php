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
    * Classe de mapeamento da tabela ima.caged_sub_divisao
    * Data de Criação: 18/04/2008

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-tabelas

    $Id: TIMACagedSubDivisao.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ima.caged_sub_divisao
  * Data de Criação: 18/04/2008

  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TIMACagedSubDivisao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TIMACagedSubDivisao()
{
    parent::Persistente();
    $this->setTabela("ima.caged_sub_divisao");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_configuracao,cod_sub_divisao');

    $this->AddCampo('cod_configuracao','integer',true  ,'',true,'TIMAConfiguracaoCaged');
    $this->AddCampo('cod_sub_divisao' ,'integer',true  ,'',true,'TPessoalSubDivisao');

}

function montaRecuperaRelacionamento()
{
    $stSql  = "SELECT caged_sub_divisao.*                                               \n";
    $stSql .= "     , sub_divisao.descricao                                             \n";
    $stSql .= "     , sub_divisao.cod_regime                                            \n";
    $stSql .= "  FROM ima.caged_sub_divisao                   \n";
    $stSql .= "     , pessoal.sub_divisao                     \n";
    $stSql .= " WHERE caged_sub_divisao.cod_sub_divisao = sub_divisao.cod_sub_divisao   \n";

    return $stSql;
}

}
?>
