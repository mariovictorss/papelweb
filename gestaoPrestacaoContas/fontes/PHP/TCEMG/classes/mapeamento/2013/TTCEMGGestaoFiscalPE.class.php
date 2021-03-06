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

    * Classe de mapeamento para gestao fiscal de medidas
    * Data de Criação: 30/07/2013
    * @author Desenvolvedor: Carolina Schwaab Marcal
    * $Revision: $
    * $Name: $
    * $Author: $
    * $Date:  $
    * $Id: $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
class TTCEMGGestaoFiscalPE extends Persistente
{
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('tcemg.medidas');
        $this->setCampoCod('cod_medida');

        $this->AddCampo('cod_medida', 'integer', true, '', true,  false);
        $this->AddCampo('cod_poder', 'integer', true, '', true,  false);
        $this->AddCampo('cod_mes', 'integer', true, '', true,  false);
        $this->AddCampo('riscos_fiscais', 'booleam', false, '', true,  false);
        $this->AddCampo('metas_fiscais', 'booleam', false, '', true,  false);
        $this->AddCampo('contratacao_aro', 'booleam', false, '', true,  false);
        $this->AddCampo('descricao', 'varchar', true, '', false, false);
    }

    function recuperaDados(&$rsRecordSet, $stFiltro)
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaDados().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }

    function montaRecuperaDados()
    {
        $stSql  = "
                    SELECT ".$this->getDado('periodo')." as periodo
                              , poder_publico.nome as poder_publico
                              , medidas.descricao as medida
                              , medidas.cod_poder
                              , medidas.cod_mes
                              , medidas.riscos_fiscais
                              , medidas.metas_fiscais
                              , medidas.contratacao_aro
                              , medidas.cod_medida
                      FROM  tcemg.medidas
              INNER JOIN administracao.mes
                          ON mes.cod_mes = medidas.cod_mes
              INNER JOIN administracao.poder_publico
                          ON poder_publico.cod_poder = medidas.cod_poder
             ";
    
        return $stSql;
    }
    
    public function __destruct()
    {

    }

}

?>