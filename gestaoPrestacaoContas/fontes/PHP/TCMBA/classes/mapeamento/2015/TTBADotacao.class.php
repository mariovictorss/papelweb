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
    * Extensão da Classe de mapeamento
    * Data de Criação: 05/07/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 62828 $
    $Name$
    $Author: domluc $
    $Date: 2008-08-18 10:43:34 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.03.00
*/

/*
$Log$
Revision 1.1  2007/07/06 02:02:49  diego
Primeira versão.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoDespesa.class.php" );

/**
  *
  * Data de Criação: 05/07/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTBADotacao extends TOrcamentoDespesa
{
/**
    * Método Construtor
    * @access Private
*/
function TTBADotacao()
{
    parent::TOrcamentoDespesa();

    $this->setDado('exercicio', Sessao::getExercicio() );
}

function recuperaDadosTribunal(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDadosTribunal().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosTribunal()
{
    $stSql .= " SELECT   desp.exercicio                                                     \n";
    $stSql .= "         ,desp.num_orgao                                                     \n";
    $stSql .= "         ,desp.num_unidade                                                   \n";
    $stSql .= "         ,desp.cod_funcao                                                    \n";
    $stSql .= "         ,desp.cod_subfuncao                                                 \n";
    $stSql .= "         ,desp.cod_programa                                                  \n";
    $stSql .= "         ,desp.num_pao                                                       \n";
    $stSql .= "         ,replace(cont.cod_estrutural,'.','') as estrutural                  \n";
    $stSql .= "         ,orcamento.fn_consulta_tipo_pao(desp.exercicio,desp.num_pao) as tipo_pao    \n";
    $stSql .= "         ,desp.cod_recurso                                                   \n";
    $stSql .= "         ,desp.vl_original                                                   \n";
    $stSql .= " FROM     orcamento.despesa          as desp                                 \n";
    $stSql .= "         ,orcamento.conta_despesa    as cont                                 \n";
    $stSql .= " WHERE   desp.exercicio = cont.exercicio                                     \n";
    $stSql .= " AND     desp.cod_conta = cont.cod_conta                                     \n";
    $stSql .= " AND     desp.exercicio='".$this->getDado('exercicio')."'                    \n";
    $stSql .= " ORDER BY  desp.exercicio                                                    \n";
    $stSql .= "         ,desp.num_orgao                                                     \n";
    $stSql .= "         ,desp.num_unidade                                                   \n";
    $stSql .= "         ,desp.cod_funcao                                                    \n";
    $stSql .= "         ,desp.cod_subfuncao                                                 \n";
    $stSql .= "         ,desp.cod_programa                                                  \n";
    $stSql .= "         ,replace(cont.cod_estrutural,'.','')                                \n";
    $stSql .= "         ,desp.cod_recurso                                                   \n";

    return $stSql;
}

}
