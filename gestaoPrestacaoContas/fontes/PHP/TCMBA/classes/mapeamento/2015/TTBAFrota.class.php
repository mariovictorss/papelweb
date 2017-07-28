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
    * Data de Criação: 29/08/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 62828 $
    $Name$
    $Author: domluc $
    $Date: 2008-08-18 10:43:34 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.05.00
*/

/*
$Log$
Revision 1.2  2007/10/02 18:17:17  hboaventura
inclusão do caso de uso uc-06.05.00

Revision 1.1  2007/09/04 00:32:40  diego
Primeira versão.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 29/08/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTBAFrota extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTBAFrota()
{
    $this->setEstrutura( array() );
    $this->setEstruturaAuxiliar( array() );
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
            SELECT veiculo.placa
                 , tipo_veiculo_vinculo.cod_tipo_tcm AS tipo_veiculo
                 , veiculo.cod_marca AS marca
                 , ( SELECT CASE WHEN ( SELECT count(1)
                                          FROM frota.veiculo_combustivel AS veiculo_combustivel_count
                                         WHERE veiculo_combustivel_count.cod_veiculo = veiculo_combustivel.cod_veiculo ) > 1
                                 THEN 5
                                 ELSE tipo_combustivel_vinculo.cod_tipo_tcm
                            END AS cod_combustivel
                       FROM frota.veiculo_combustivel
                  LEFT JOIN tcmba.tipo_combustivel_vinculo
                         ON tipo_combustivel_vinculo.cod_combustivel = veiculo_combustivel.cod_combustivel
                      WHERE veiculo_combustivel.cod_veiculo = veiculo.cod_veiculo
                      LIMIT 1
                   ) AS combustivel
                 , veiculo.num_certificado AS num_renavam
                 , veiculo.chassi
                 , veiculo.ano_fabricacao
                 , veiculo_propriedade.proprio
                 , bem_comprado.nota_fiscal
                 , bem.vl_bem
                 , '1' AS anterior_siga
                 , bem_comprado.cod_empenho AS empenho
                 , TO_CHAR(veiculo.dt_aquisicao,'dd/mm/yyyy') AS dt_aquisicao
                 , TO_CHAR(veiculo_baixado.dt_baixa,'dd/mm/yyyy') AS dt_baixa
              FROM frota.veiculo
         LEFT JOIN tcmba.tipo_veiculo_vinculo
                ON tipo_veiculo_vinculo.cod_tipo = veiculo.cod_tipo_veiculo
         LEFT JOIN tcmba.marca
                ON marca.cod_marca = veiculo.cod_marca
               AND marca.cod_tipo_tcm = tipo_veiculo_vinculo.cod_tipo_tcm
         LEFT JOIN ( SELECT veiculo_propriedade.cod_veiculo
                          , proprio.cod_bem
                          , CASE WHEN (proprio = true)
                                 THEN 'S'
                                 ELSE 'N'
                            END AS proprio
                       FROM frota.veiculo_propriedade
                 INNER JOIN ( SELECT cod_veiculo
                                   , MAX(timestamp) AS timestamp
                                FROM frota.veiculo_propriedade
                            GROUP BY cod_veiculo
                            ) AS veiculo_propriedade_max
                         ON veiculo_propriedade_max.cod_veiculo = veiculo_propriedade.cod_veiculo
                        AND veiculo_propriedade_max.timestamp = veiculo_propriedade.timestamp
                  LEFT JOIN frota.proprio
                         ON proprio.cod_veiculo = veiculo_propriedade.cod_veiculo
                        AND proprio.timestamp = veiculo_propriedade.timestamp
                   ) AS veiculo_propriedade
                ON veiculo_propriedade.cod_veiculo = veiculo.cod_veiculo
         LEFT JOIN patrimonio.bem
                ON bem.cod_bem = veiculo_propriedade.cod_bem
         LEFT JOIN patrimonio.bem_comprado
                ON bem_comprado.cod_bem = bem.cod_bem
         LEFT JOIN frota.veiculo_baixado
                ON veiculo_baixado.cod_veiculo = veiculo.cod_veiculo
             WHERE (   veiculo.dt_aquisicao BETWEEN TO_DATE('".$this->getDado('dt_inicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_fim')."','dd/mm/yyyy')
                    OR veiculo_baixado.dt_baixa BETWEEN TO_DATE('".$this->getDado('dt_inicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_fim')."','dd/mm/yyyy') )
    ";
    if ( $this->getDado('cod_entidade') != '' ) {
        $stSql .= " AND bem_comprado.cod_entidade = ".$this->getDado('cod_entidade')." ";
    }

    return $stSql;
}

}
