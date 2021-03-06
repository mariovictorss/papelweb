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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEMGDividaConsolidadaRPPS extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEMGDividaConsolidadaRPPS()
    {
        parent::Persistente();
    }

    public function montaRecuperaTodos()
    {
        $stSql = " SELECT 
                        '12' as mes
                        , ABS(div_contratual_demais         ) as div_contratual_demais
                        , ABS(div_contratual_ppp            ) as div_contratual_ppp
                        , ABS(div_mobiliaria                ) as div_mobiliaria
                        , ABS(op_credito_inf_12             ) as op_credito_inf_12
                        , ABS(outras                        ) as outras
                        , ABS(parc_contr_sociais_prev       ) as parc_contr_sociais_prev
                        , ABS(parc_contr_sociais_demais     ) as parc_contr_sociais_demais
                        , ABS(parc_tributos                 ) as parc_tributos
                        , ABS(parc_fgts                     ) as parc_fgts
                        , ABS(precatorios_post              ) as precatorios_post
                        , ABS(div_contratual_demais_rpps    ) as div_contratual_demais_rpps
                        , ABS(div_contratual_ppp_rpps       ) as div_contratual_ppp_rpps
                        , ABS(div_mobiliaria_rpps           ) as div_mobiliaria_rpps
                        , ABS(op_credito_inf_12_rpps        ) as op_credito_inf_12_rpps
                        , ABS(outras_rpps                   ) as outras_rpps
                        , ABS(parc_contr_sociais_prev_rpps  ) as parc_contr_sociais_prev_rpps
                        , ABS(parc_contr_sociais_demais_rpps) as parc_contr_sociais_demais_rpps
                        , ABS(parc_tributos_rpps            ) as parc_tributos_rpps
                        , ABS(parc_fgts_rpps                ) as parc_fgts_rpps
                        , ABS(precatorios_post_rpps         ) as precatorios_post_rpps
                    FROM tcemg.arquivo_divida_consolidada_rpps('".$this->getDado('exercicio')."'                                                                
                                                                ,'".$this->getDado('cod_entidade')."'
                                                                ,'".$this->getDado('cod_entidade_rpps')."') 
                    AS tbl(
                             div_contratual_demais              NUMERIC(14,2)
                            , div_contratual_ppp                NUMERIC(14,2)
                            , div_mobiliaria                    NUMERIC(14,2)
                            , op_credito_inf_12                 NUMERIC(14,2)
                            , outras                            NUMERIC(14,2)
                            , parc_contr_sociais_prev           NUMERIC(14,2)
                            , parc_contr_sociais_demais         NUMERIC(14,2)
                            , parc_tributos                     NUMERIC(14,2)
                            , parc_fgts                         NUMERIC(14,2)
                            , precatorios_post                  NUMERIC(14,2)
                            , div_contratual_demais_rpps        NUMERIC(14,2)
                            , div_contratual_ppp_rpps           NUMERIC(14,2)
                            , div_mobiliaria_rpps               NUMERIC(14,2)
                            , op_credito_inf_12_rpps            NUMERIC(14,2)
                            , outras_rpps                       NUMERIC(14,2)
                            , parc_contr_sociais_prev_rpps      NUMERIC(14,2)
                            , parc_contr_sociais_demais_rpps    NUMERIC(14,2)
                            , parc_tributos_rpps                NUMERIC(14,2)
                            , parc_fgts_rpps                    NUMERIC(14,2)
                            , precatorios_post_rpps             NUMERIC(14,2)
                    )
        ";
        
        return $stSql;
    }

}
