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
    * Classe de mapeamento da tabela compras.fornecedor_inativacao
    * Data de Criação: 30/06/2006

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Leandro André Zis

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 17467 $
    $Name$
    $Author: larocca $
    $Date: 2006-11-07 14:41:27 -0200 (Ter, 07 Nov 2006) $

    * Casos de uso: uc-03.04.03
*/

/*
$Log$
Revision 1.6  2006/11/07 16:41:27  larocca
Inclusão dos Casos de Uso

Revision 1.5  2006/09/29 17:35:31  fernando
implementado a alteração do UC-03.04.03

Revision 1.4  2006/07/06 14:05:54  diego
Retirada tag de log com erro.

Revision 1.3  2006/07/06 12:11:10  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  compras.fornecedor_inativacao
  * Data de Criação: 30/06/2006

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Leandro André Zis

  * @package URBEM
  * @subpackage Mapeamento
*/
class TComprasFornecedorInativacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TComprasFornecedorInativacao()
{
    parent::Persistente();
    $this->setTabela("compras.fornecedor_inativacao");

    $this->setCampoCod('');
    $this->setComplementoChave('cgm_fornecedor,timestamp_inicio');

    $this->AddCampo('cgm_fornecedor','integer',true,'',true,'TComprasFornecedor');
    $this->AddCampo('timestamp_inicio','timestamp',true,'',true,false);
    $this->AddCampo('timestamp_fim','timestamp',false,'',false,false);
    $this->AddCampo('motivo','varchar',true,'200',false,false);

}

function recuperaUltimosFornecedores(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaUltimosFornecedores().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}
function montaRecuperaUltimosFornecedores()
{
$stSql .="SELECT                                                     \n";
$stSql .="    coalesce(cfi.cgm_fornecedor,null) as cgm_fornecedor    \n";
$stSql .="   ,cfi.timestamp_inicio                                   \n";
$stSql .="   ,cfi.timestamp_fim                                      \n";
$stSql .="   ,cfi.motivo                                             \n";
$stSql .="FROM                                                       \n";
$stSql .="   compras.fornecedor_inativacao as cfi                    \n";
$stSql .="   ,(SELECT                                                \n";
$stSql .="        max(timestamp_inicio) as timestamp_inicio          \n";
$stSql .="        ,cgm_fornecedor                                    \n";
$stSql .="      FROM                                                 \n";
$stSql .="         compras.fornecedor_inativacao                     \n";
$stSql .="      GROUP BY                                             \n";
$stSql .="         cgm_fornecedor                                    \n";
$stSql .="     ) as ativacao                                         \n";
$stSql .="WHERE                                                      \n";
$stSql .="         ativacao.cgm_fornecedor = cfi.cgm_fornecedor      \n";
$stSql .="    AND  ativacao.timestamp_inicio = cfi.timestamp_inicio  \n";
return $stSql;

}

}
