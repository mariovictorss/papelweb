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
    * Classe de mapeamento da tabela licitacao.participante_certificacao_licitacao
    * Data de Criação: 17/08/2015

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Arthur Cruz

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 18419 $
    $Name$
    $Author: hboaventura $
    $Date: 2006-11-30 17:37:04 -0200 (Qui, 30 Nov 2006) $

    * Casos de uso: uc-03.05.14
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TLicitacaoParticipanteCertificacaoLicitacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
public function __construct()
{
    parent::Persistente();
    $this->setTabela("licitacao.participante_certificacao_licitacao");

    $this->setCampoCod('num_certificacao');
    $this->setComplementoChave('num_certificacao, exercicio_certificacao, cgm_fornecedor, cod_entidade, exercicio_licitacao');
    
    $this->AddCampo('num_certificacao'       ,'integer' ,true ,''  ,true ,true);
    $this->AddCampo('exercicio_certificacao' ,'char'    ,true ,'4' ,true ,true);
    $this->AddCampo('cgm_fornecedor'         ,'integer' ,true ,''  ,true ,true);
    $this->AddCampo('cod_licitacao'          ,'integer' ,true ,''  ,true ,true);
    $this->AddCampo('cod_modalidade'         ,'integer' ,true ,''  ,true ,true);
    $this->AddCampo('cod_entidade'           ,'integer' ,true ,''  ,true ,true);
    $this->AddCampo('exercicio_licitacao'    ,'char'    ,true ,'4' ,true ,true);

}

}

?>