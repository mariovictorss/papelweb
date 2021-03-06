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
    * Classe de mapeamento da tabela FN_EXPORTACAO_BMOVANT
    * Data de Criação: 24/01/2005

    * @author Desenvolvedor: Cleisson Barboza
    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: cako $
    $Date: 2006-07-17 11:32:12 -0300 (Seg, 17 Jul 2006) $

    * Casos de uso: uc-02.08.07
*/

/*
$Log$
Revision 1.3  2006/07/17 14:31:44  cako
Bug #6013#

Revision 1.2  2006/07/05 20:45:59  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FExportacaoBmovant extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FExportacaoBmovAnt()
{
    parent::Persistente();
    $this->setTabela('tcers.fn_exportacao_bmovant');
}

function montaRecuperaDadosExportacao()
{
    $stSql .= " SELECT                                                                      \n";
    $stSql .= "     *                                                                       \n";
    $stSql .= " FROM                                                                        \n";
    $stSql .= " ".$this->getTabela()."('".$this->getDado("stExercicio")."',                 \n";
    $stSql .= " '".$this->getDado("stCodEntidades")."')                                     \n";
    $stSql .= " AS ( cod_conta varchar, cod_entidade varchar, mov_deb_1 numeric(14,2),      \n";
    $stSql .= " mov_cre_1 numeric(14,2), mov_deb_2 numeric(14,2), mov_cre_2 numeric(14,2),  \n";
    $stSql .= " mov_deb_3 numeric(14,2), mov_cre_3 numeric(14,2), mov_deb_4 numeric(14,2),  \n";
    $stSql .= " mov_cre_4 numeric(14,2), mov_deb_5 numeric(14,2), mov_cre_5 numeric(14,2),  \n";
    $stSql .= " mov_deb_6 numeric(14,2), mov_cre_6 numeric(14,2))     \n";
//  echo $stSql;
    return $stSql;
}

/**
    * Executa funcao fn_exportacao_bmovant no banco de dados a partir do comando SQL montado no método montaRecuperaDadosExportacao
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaDadosExportacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaDadosExportacao().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

}
