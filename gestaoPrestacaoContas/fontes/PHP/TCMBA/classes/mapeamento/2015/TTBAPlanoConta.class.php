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
    * Data de Criação: 08/06/2007

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
Revision 1.2  2007/10/03 02:50:44  diego
Corrigindo formatação

Revision 1.1  2007/06/22 22:50:29  diego
Primeira versão.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );
include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoConta.class.php" );

/**
  *
  * Data de Criação: 05/02/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTBAPlanoConta extends TContabilidadePlanoConta
{
/**
    * Método Construtor
    * @access Private
*/
function TTBAPlanoConta()
{
    parent::TContabilidadePlanoConta();

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
    $stSql .= " SELECT   pc.exercicio                                                                               \n";
    $stSql .= "         ,pc.cod_estrutural                                                                          \n";
    $stSql .= "         ,pc.cod_conta                                                                               \n";
    $stSql .= "         ,case when substr(replace(pc.cod_estrutural,'.',''),1,14) like '1111%'  then 1 /*Banco*/    \n";
    $stSql .= "               when substr(replace(pc.cod_estrutural,'.',''),1,14) like '3%'     then 2 /*Despesa*/  \n";
    $stSql .= "               when substr(replace(pc.cod_estrutural,'.',''),1,14) like '4%'     then 3 /*Receita*/  \n";
    $stSql .= "               when substr(replace(pc.cod_estrutural,'.',''),1,14) like '1%'     then 5 /*Ativo*/    \n";
    $stSql .= "               when substr(replace(pc.cod_estrutural,'.',''),1,14) like '2%'     then 6 /*Passivo*/  \n";
    $stSql .= "          else 9 /*Outras Contas*/                                                                   \n";
    $stSql .= "         end as tipo_conta                                                                           \n";
    $stSql .= "         ,pc.nom_conta                                                                               \n";
    $stSql .= "         ,'M' as origem_saldo /*verificar pode ser somente Mista*/                                   \n";
    $stSql .= "         ,case when contabilidade.fn_tipo_conta_plano(pc.exercicio, pc.cod_estrutural) = 'A' then 1  \n";
    $stSql .= "          else 2                                                                                     \n";
    $stSql .= "         end as recebe_lancamento                                                                    \n";
    $stSql .= "         ,substr(cod_banco,1,3)  as banco                                                            \n";
    $stSql .= "         ,trim(cod_agencia)      as agencia                                                          \n";
    $stSql .= "         ,trim(conta_corrente)   as conta_corrente                                                   \n";
    $stSql .= " FROM     contabilidade.plano_conta as pc                                                            \n";
    $stSql .= "         LEFT JOIN                                                                                   \n";
    $stSql .= "          contabilidade.plano_analitica as pa                                                        \n";
    $stSql .= "         ON ( pc.exercicio=pa.exercicio AND pc.cod_conta=pa.cod_conta )                              \n";
    $stSql .= "         LEFT JOIN                                                                                   \n";
    $stSql .= "          contabilidade.plano_banco as pb                                                            \n";
    $stSql .= "         ON ( pa.exercicio=pb.exercicio AND pa.cod_plano=pb.cod_plano )                              \n";
    $stSql .= " WHERE   pc.exercicio='".$this->getDado('exercicio')."'                                              \n";
    if (trim($this->getDado('stEntidades'))) {
        $stSql .= " AND     pb.cod_entidade IN (".$this->getDado('stEntidades').") \n";
    }
    $stSql .= " ORDER BY pc.exercicio                                                                               \n";
    $stSql .= "         ,pc.cod_estrutural                                                                          \n";

    return $stSql;
}

}
