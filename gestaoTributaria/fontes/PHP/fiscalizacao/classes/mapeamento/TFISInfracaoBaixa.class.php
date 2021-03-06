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
 * Classe de mapeamento para fiscalizacao.infracao_baixa
 * Data de Criação: 26/05/2009

 * @author Fernando Piccini Cercato

 * @package URBEM
 * @subpackage Mapeamento

 $Id: $

 * Casos de uso: uc-05.07.06
 */

/**
 * Classe de mapeamento para infracao_baixa.
 */
class TFISInfracaoBaixa extends Persistente
{
    /**
     * Método construtor
     * @access public
     */
    public function __construct()
    {
        parent::__construct();

        $this->setTabela( 'fiscalizacao.infracao_baixa' );

        $this->setCampoCod( '' );
        $this->setComplementoChave( 'cod_infracao,timestamp_inicio' );

        $this->addCampo( 'cod_infracao', 'integer', true, '', true, true );
        $this->addCampo( 'timestamp_inicio', 'timestamp', false, '', true, false );
        $this->addCampo( 'timestamp_termino', 'timestamp', false, '', false, false );
        $this->addCampo( 'motivo', 'text', false, '', false, false );
    }

    public function recuperaMaxTimestampInfracao(&$rsRecordSet, $inCodInfracao, $boTransacao = "")
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();

        $stSQL = $this->montaRecuperaMaxTimestampInfracao( $inCodInfracao );

        return $obConexao->executaSQL( $rsRecordSet, $stSQL, $boTransacao );
    }

    private function montaRecuperaMaxTimestampInfracao($inCodInfracao)
    {
        $stSQL  = "
            SELECT MAX( timestamp_inicio ) AS timestamp_inicio
              FROM fiscalizacao.infracao_baixa
             WHERE cod_infracao = ".$inCodInfracao;

        return $stSQL;
    }
}
