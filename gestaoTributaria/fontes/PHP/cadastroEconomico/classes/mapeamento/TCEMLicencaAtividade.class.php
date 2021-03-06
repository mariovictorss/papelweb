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
  * Classe de mapeamento da tabela ECONOMICO.LICENCA_ATIVIDADE
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TCEMLicencaAtividade.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.02.12
*/

/*
$Log$
Revision 1.8  2006/10/23 16:21:00  dibueno
Alterações no SQL para exibição de atividade principal, observação e ocorrencia_licenca

Revision 1.7  2006/09/15 12:08:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ECONOMICO.LICENCA_ATIVIDADE
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCEMLicencaAtividade extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCEMLicencaAtividade()
{
    parent::Persistente();
    $this->setTabela('economico.licenca_atividade');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_licenca,exercicio,cod_atividade,inscricao_economica,ocorrencia_atividade,ocorrencia_licenca');

    $this->AddCampo('cod_licenca','integer',true,'',true,true);
    $this->AddCampo('exercicio','char',true,'4',true,true);
    $this->AddCampo('cod_atividade','integer',true,'4',true,true);
    $this->AddCampo('inscricao_economica','integer',true,'',true,true);
    $this->AddCampo('ocorrencia_atividade','integer',true,'',true,true);
    $this->AddCampo('ocorrencia_licenca','integer',true,'',true,true);
    $this->AddCampo('dt_inicio','date',true,'4',false,false);
    $this->AddCampo('dt_termino','date',false,'4',false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql  = "SELECT                                                				\n";
    $stSql .= "    LCA.COD_LICENCA,                                  				\n";
    $stSql .= "    LCA.EXERCICIO,                                    				\n";
    $stSql .= "    LCA.INSCRICAO_ECONOMICA,                          				\n";
    $stSql .= "    ELO.OBSERVACAO,			                          				\n";
    $stSql .= "    LCA.COD_ATIVIDADE,                                				\n";
    $stSql .= "    LCA.OCORRENCIA_ATIVIDADE,                         				\n";
    $stSql .= "    LCA.OCORRENCIA_LICENCA,                           				\n";
    $stSql .= "    ACE.PRINCIPAL,                                    				\n";
    $stSql .= "    TO_CHAR(ACE.DT_INICIO ,'DD/MM/YYYY') AS DT_INICIO_ATIVIDADE, 	\n";
    $stSql .= "    TO_CHAR(ACE.DT_TERMINO,'DD/MM/YYYY') AS DT_TERMINO_ATIVIDADE,	\n";
    $stSql .= "    AT.NOM_ATIVIDADE                                  			\n";
    $stSql .= "FROM                                                  			\n";
    $stSql .= "    economico.licenca_atividade AS LCA                			\n";
    $stSql .= "LEFT JOIN                                             			\n";
    $stSql .= "    economico.atividade_cadastro_economico AS ACE     			\n";
    $stSql .= "ON                                                    			\n";
    $stSql .= "    LCA.COD_ATIVIDADE = ACE.COD_ATIVIDADE AND         			\n";
    $stSql .= "    LCA.inscricao_economica = ACE.inscricao_economica 			\n";
    $stSql .= "LEFT JOIN                                             			\n";
    $stSql .= "    economico.atividade AS AT                         			\n";
    $stSql .= "ON                                                    			\n";
    $stSql .= "    LCA.COD_ATIVIDADE = AT.COD_ATIVIDADE              			\n";
    $stSql .= " LEFT JOIN														\n";
    $stSql .= "		economico.licenca_observacao as ELO							\n";
    $stSql .= "	ON																\n";
    $stSql .= "		ELO.cod_licenca = LCA.cod_licenca 							\n";

    return $stSql;
}

}
