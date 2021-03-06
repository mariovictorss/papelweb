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
* Classe de mapeamento da tabela ima.motvos_rejeicao_consignacao_emprestimo_banrisul
* Data de Criação   : 10/10/2009
*
* @author Analista      Dagine Rodrigues Vieira
* @author Desenvolvedor Cassiano de Vasconcellos Ferreira
*
* @package URBEM
* @subpackage
*
* @ignore
* $Id:$
 *
 */
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TIMAMotivosRejeicaoConsignacaoEmprestimoBanrisul extends Persistente
{
    public function TIMAConsignacaoEmprestimoBanrisul()
    {
        parent::Persistente();
        $this->setTabela('ima.consignacao_emprestimo_banrisul');

        $this->setCampoCod('');
        $this->setComplementoChave('');
        $this->AddCampo( 'cod_motivo', 'char', false, 2, false, '' );
        $this->AddCampo( 'descricao_motivo', 'varchar', false, 200, false, '' );
    }

}
?>
