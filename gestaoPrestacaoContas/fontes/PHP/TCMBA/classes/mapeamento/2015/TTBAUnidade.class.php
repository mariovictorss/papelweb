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
    * Data de Criação: 13/06/2007

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
Revision 1.2  2007/09/27 12:53:57  hboaventura
adicionando arquivos

Revision 1.1  2007/06/22 22:50:29  diego
Primeira versão.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoUnidade.class.php" );

/**
  *
  * Data de Criação: 13/06/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTBAUnidade extends TOrcamentoUnidade
{
/**
    * Método Construtor
    * @access Private
*/
function TTBAUnidade()
{
    parent::TOrcamentoUnidade();

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
    $stSql .= "
        SELECT unidade.exercicio
             , unidade.num_unidade
             , unidade.nom_unidade
             , unidade.num_orgao
             , configuracao_entidade.valor as cod_unidade_gestora
          FROM orcamento.unidade
    INNER JOIN orcamento.despesa
            ON despesa.num_unidade = unidade.num_unidade
           AND despesa.exercicio = unidade.exercicio
    INNER JOIN administracao.configuracao_entidade
            ON configuracao_entidade.cod_entidade = despesa.cod_entidade
           AND configuracao_entidade.parametro = 'tcm_unidade_gestora'
         WHERE despesa.cod_entidade = ".$this->getDado('cod_entidade')."
           AND despesa.exercicio = '".$this->getDado('exercicio')."'
      GROUP BY unidade.exercicio
             , unidade.num_unidade
             , unidade.nom_unidade
             , unidade.num_orgao
             , configuracao_entidade.valor
    ";

    return $stSql;
}

}
