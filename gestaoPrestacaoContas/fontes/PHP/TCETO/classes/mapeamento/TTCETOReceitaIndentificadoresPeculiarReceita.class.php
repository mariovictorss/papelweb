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
    * Pacote de configuração do TCETO - Mapeamento tceto.receita_indentificadores_peculiar_receita
    * Data de Criação   : 07/11/2014

    * @author Analista: Silvia Martins Silva
    * @author Desenvolvedor: Michel Teixeira
    * $Id: TTCETOReceitaIndentificadoresPeculiarReceita.class.php 60673 2014-11-07 15:10:07Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once (CLA_PERSISTENTE);

class TTCETOReceitaIndentificadoresPeculiarReceita extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCETOReceitaIndentificadoresPeculiarReceita()
    {
        parent::Persistente();
        $this->setTabela('tceto.receita_indentificadores_peculiar_receita');
        $this->setComplementoChave('exercicio, cod_receita');

        $this->AddCampo('exercicio'         ,'varchar',true,'4' ,true ,true );
        $this->AddCampo('cod_receita'       ,'integer',true,''  ,true ,true );
        $this->AddCampo('cod_identificador' ,'integer',true,''  ,false,false);
    }

    public function montaRecuperaRelacionamento()
    {
        $stQuebra = "\n";
        $stSql  = " SELECT                                                                          ".$stQuebra;
        $stSql .= "     trim(CR.descricao) as descricao,                                            ".$stQuebra;
        $stSql .= "     O.cod_receita,                                                              ".$stQuebra;
        $stSql .= "     valores_identificadores.cod_identificador,                                  ".$stQuebra;
        $stSql .= "     valores_identificadores.descricao as caracteristica                         ".$stQuebra;
        $stSql .= " FROM                                                                            ".$stQuebra;
        $stSql .= "     orcamento.vw_classificacao_receita AS CR,                                   ".$stQuebra;
        $stSql .= "     orcamento.recurso('".$this->getDado('exercicio')."') AS R,                  ".$stQuebra;
        $stSql .= "     orcamento.receita        AS O                                               ".$stQuebra;
        
        $stSql .= "     LEFT JOIN tceto.receita_indentificadores_peculiar_receita                   ".$stQuebra;
        $stSql .= "     ON (receita_indentificadores_peculiar_receita.exercicio  = o.exercicio      ".$stQuebra;
        $stSql .= "    AND receita_indentificadores_peculiar_receita.cod_receita = o.cod_receita )  ".$stQuebra;

        $stSql .= "     LEFT JOIN tceto.valores_identificadores                                     ".$stQuebra;
        $stSql .= "     ON (valores_identificadores.cod_identificador = receita_indentificadores_peculiar_receita.cod_identificador ) ".$stQuebra;

        $stSql .= " WHERE                                                                           ".$stQuebra;
        $stSql .= "         CR.exercicio IS NOT NULL                                                ".$stQuebra;
        $stSql .= "     AND O.cod_conta   = CR.cod_conta                                            ".$stQuebra;
        $stSql .= "     AND O.exercicio   = CR.exercicio                                            ".$stQuebra;
        $stSql .= "     AND O.cod_recurso = R.cod_recurso                                           ".$stQuebra;
        $stSql .= "     AND O.exercicio   = R.exercicio                                             ".$stQuebra;

        return $stSql;
    }
    
    public function __destruct(){}

}
