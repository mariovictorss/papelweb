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
    * Data de Criação: 18/04/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTCMGOExtraOrcamentarias.class.php 56934 2014-01-08 19:46:44Z gelson $

    * Casos de uso: uc-06.04.00
*/

include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoContaReceita.class.php" );

class TTCEMGExtraOrcamentarias extends TOrcamentoContaReceita
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCMGOExtraOrcamentarias()
    {
        parent::TOrcamentoContaReceita();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    public function recuperaExportacao10(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaExportacao10",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao10()
    {
        $stSql = "
                SELECT *
                  FROM (
                        -- select para as receitas
                        SELECT
                                10 AS tipo_registro
                              , balancete_extmmaa.cod_plano AS cod_ext
                              , LPAD(configuracao_entidade.valor::VARCHAR,2,'0') AS cod_orgao
                              , LPAD((LPAD(''||configuracao_entidade.valor,2, '0')||LPAD(''||configuracao_entidade.cod_entidade,2, '0')), 5, '0') AS cod_unidade 
                              , LPAD(balancete_extmmaa.tipo_lancamento::VARCHAR,2,'0') as tipo_lancamento
                              , LPAD(balancete_extmmaa.sub_tipo_lancamento::VARCHAR,4,'0') AS sub_tipo
                              , ' ' AS desdobra_sub_tipo
                              , remove_acentos(plano_conta.nom_conta) AS desc_extra_orc
                              
                          FROM tesouraria.transferencia
                          
                          JOIN contabilidade.plano_analitica
                            ON plano_analitica.cod_plano = transferencia.cod_plano_credito
                           AND plano_analitica.exercicio = transferencia.exercicio
                           
                          JOIN tcemg.balancete_extmmaa
                            ON balancete_extmmaa.cod_plano = plano_analitica.cod_plano
                           AND balancete_extmmaa.exercicio = plano_analitica.exercicio
                           
                          JOIN contabilidade.plano_conta
                            ON plano_analitica.exercicio = plano_conta.exercicio
                           AND plano_analitica.cod_conta = plano_conta.cod_conta
                           
                          JOIN (
                                SELECT lote.exercicio
                                     , conta_credito.cod_plano
                                     , lote.tipo
                                     , lote.cod_entidade
                                     , SUM(valor_lancamento.vl_lancamento) AS vl_lancamento
                                      
                                  FROM contabilidade.lote
                                  
				  JOIN contabilidade.valor_lancamento
				    ON valor_lancamento.exercicio = lote.exercicio
				   AND valor_lancamento.cod_entidade = lote.cod_entidade
				   AND valor_lancamento.tipo = lote.tipo
				   AND valor_lancamento.cod_lote = lote.cod_lote
				   AND valor_lancamento.tipo_valor = 'C'
                                   
				  JOIN contabilidade.conta_credito
				    ON conta_credito.exercicio = valor_lancamento.exercicio
				   AND conta_credito.cod_entidade = valor_lancamento.cod_entidade
				   AND conta_credito.tipo = valor_lancamento.tipo
				   AND conta_credito.cod_lote = valor_lancamento.cod_lote
				   AND conta_credito.sequencia = valor_lancamento.sequencia
				   AND valor_lancamento.tipo = 'T'
                                   
				 WHERE lote.exercicio = '".$this->getDado('exercicio')."' AND lote.tipo = 'T'
				   AND lote.dt_lote BETWEEN TO_DATE('".$this->getDado('dt_inicial')."', 'dd/mm/yyyy') and TO_DATE('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
                              GROUP BY 1,2,3,4
			    ) AS valor
                            ON valor.exercicio = plano_analitica.exercicio
                           AND valor.cod_plano = plano_analitica.cod_plano
                           AND valor.tipo = transferencia.tipo
		           AND valor.cod_entidade = transferencia.cod_entidade 
                           
                          JOIN administracao.configuracao_entidade
                            ON configuracao_entidade.cod_entidade = valor.cod_entidade
                           AND configuracao_entidade.exercicio = valor.exercicio
                           AND configuracao_entidade.cod_modulo = 55
                           AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
                           
                         WHERE balancete_extmmaa.exercicio = '".$this->getDado('exercicio')."'
                           AND transferencia.cod_entidade IN (".$this->getDado('entidades').")
                           AND transferencia.cod_tipo = 2
                           
                         UNION
                         
                        -- select para as despesas
                        SELECT
                                10 AS tipo_registro
                              , balancete_extmmaa.cod_plano AS cod_ext
                              , LPAD(configuracao_entidade.valor::VARCHAR,2,'0') AS cod_orgao
                              , LPAD((LPAD(''||configuracao_entidade.valor,2, '0')||LPAD(''||configuracao_entidade.cod_entidade,2, '0')), 5, '0') AS cod_unidade 
                              , LPAD(balancete_extmmaa.tipo_lancamento::VARCHAR,2,'0') as tipo_lancamento
                              , LPAD(balancete_extmmaa.sub_tipo_lancamento::VARCHAR,4,'0') AS sub_tipo
                              , ' ' AS desdobra_sub_tipo
                              , remove_acentos(plano_conta.nom_conta) AS desc_extra_orc
                              
                          FROM tesouraria.transferencia
                          
                          JOIN contabilidade.plano_analitica
                            ON plano_analitica.cod_plano = transferencia.cod_plano_debito
                           AND plano_analitica.exercicio = transferencia.exercicio
                           
                          JOIN tcemg.balancete_extmmaa
                            ON balancete_extmmaa.cod_plano = plano_analitica.cod_plano
                           AND balancete_extmmaa.exercicio = plano_analitica.exercicio
                           
                          JOIN contabilidade.plano_conta
                            ON plano_analitica.exercicio = plano_conta.exercicio
                           AND plano_analitica.cod_conta = plano_conta.cod_conta
                           
                          JOIN (  
				SELECT lote.exercicio
			             , conta_debito.cod_plano
			             , lote.tipo
			             , lote.cod_entidade
			             , SUM(valor_lancamento.vl_lancamento) AS vl_lancamento
                                     
				  FROM contabilidade.lote
                                  
				  JOIN contabilidade.valor_lancamento
				    ON valor_lancamento.exercicio = lote.exercicio
				   AND valor_lancamento.cod_entidade = lote.cod_entidade
				   AND valor_lancamento.tipo = lote.tipo
				   AND valor_lancamento.cod_lote = lote.cod_lote
				   AND valor_lancamento.tipo_valor = 'D'
                                   
				  JOIN contabilidade.conta_debito
				    ON conta_debito.exercicio = valor_lancamento.exercicio
				   AND conta_debito.cod_entidade = valor_lancamento.cod_entidade
				   AND conta_debito.tipo = valor_lancamento.tipo
				   AND conta_debito.cod_lote = valor_lancamento.cod_lote
				   AND conta_debito.sequencia = valor_lancamento.sequencia
				   AND valor_lancamento.tipo = 'T'
                                   
				 WHERE lote.exercicio = '".$this->getDado('exercicio')."' AND lote.tipo = 'T'
				   AND lote.dt_lote BETWEEN TO_DATE('".$this->getDado('dt_inicial')."', 'dd/mm/yyyy') and TO_DATE('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
                              GROUP BY 1,2,3,4
                            ) AS valor
                            ON valor.exercicio = plano_analitica.exercicio
                           AND valor.cod_plano = plano_analitica.cod_plano
                           AND valor.tipo = transferencia.tipo
		           AND valor.cod_entidade = transferencia.cod_entidade 
                           
                          JOIN administracao.configuracao_entidade
                            ON configuracao_entidade.cod_entidade = valor.cod_entidade
                           AND configuracao_entidade.exercicio = valor.exercicio
                           AND configuracao_entidade.cod_modulo = 55
                           AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
                              
                         WHERE balancete_extmmaa.exercicio = '".$this->getDado('exercicio')."'
                           AND transferencia.cod_entidade IN (".$this->getDado('entidades').")
                           AND transferencia.cod_tipo = 1
                           
                    ) AS resultado
                    
                    ORDER BY cod_ext
        ";
        
        return $stSql;
    }

    public function recuperaExportacao20(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
            return $this->executaRecupera("montaRecuperaExportacao20",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao20()
    {
        $stSql = "  SELECT tipo_registro
                        , cod_orgao
                        , cod_ext
                        , cod_font_recurso
                        , vl_saldo_ant
                        , CASE SIGN(vl_saldo_ant)
                                WHEN -1 THEN 'D'
                                WHEN 1  THEN 'C'
                                WHEN 0  THEN ''
                         END AS natsaldoanteriorfonte                
                        , vl_saldo_atual
                        , CASE SIGN(vl_saldo_atual)
                                WHEN -1 THEN 'D'
                                WHEN 1  THEN 'C'
                                WHEN 0  THEN ''
                         END AS natsaldoatualfonte
                    FROM (
                            SELECT  tipo_registro
                                    , cod_orgao
                                    , cod_ext
                                    , cod_recurso AS cod_font_recurso
                                    , vl_saldo_anterior AS vl_saldo_ant
                                    , CASE WHEN (substr(cod_estrutural,1,1) = '2') THEN
			                                 (vl_saldo_atual * -1)
			                            ELSE
			                                 vl_saldo_atual
		                             END AS vl_saldo_atual
                            FROM tcemg.fn_arquivo_ext_registro20('".$this->getDado('exercicio')."'
                                                                ,'cod_entidade IN (".$this->getDado('entidades').")'
                                                                ,'".$this->getDado('dt_inicial')."'
                                                                ,'".$this->getDado('dt_final')."'
                                                                ,'A'::CHAR)
                            AS retorno( cod_estrutural    VARCHAR
                                        , tipo_registro     INTEGER
                                        , cod_orgao         VARCHAR
                                        , cod_ext           INTEGER
                                        , cod_recurso       INTEGER
                                        , vl_saldo_anterior NUMERIC
                                        , vl_saldo_debitos  NUMERIC
                                        , vl_saldo_creditos NUMERIC
                                        , vl_saldo_atual    NUMERIC
                            )
                    ) as registro20
        ";        
        return $stSql;
    }

    public function recuperaExportacao21(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaExportacao21",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao21()
    {
        $stSql = "SELECT tipo_registro
             , LPAD( (cod_ext::varchar || dt_lancamento ) ,15,'0') AS cod_reduzido_mov
             , cod_ext
             , cod_font_recurso
             , categoria
             , dt_lancamento
             , SUM(COALESCE(vl_lancamento, 0.00)) as vl_lancamento
             
        FROM (
                -- receitas
                  SELECT
                         21 AS tipo_registro                       
                       , balancete_extmmaa.cod_plano AS cod_ext
                       , plano_recurso.cod_recurso AS cod_font_recurso
                       , 1 AS categoria
                       , TO_CHAR(lote.dt_lote, 'ddmmyyyy') AS dt_lancamento
                       , CASE WHEN lote.vl_lancamento < 0.00 THEN (lote.vl_lancamento * -1) ELSE lote.vl_lancamento END AS vl_lancamento
                       
                    FROM tesouraria.transferencia
                    
                    JOIN contabilidade.plano_analitica
                      ON plano_analitica.cod_plano = transferencia.cod_plano_credito
                     AND plano_analitica.exercicio = transferencia.exercicio
                     
                    JOIN tcemg.balancete_extmmaa
                      ON balancete_extmmaa.cod_plano = plano_analitica.cod_plano
                     AND balancete_extmmaa.exercicio = plano_analitica.exercicio
                     
                    JOIN contabilidade.plano_conta
                      ON plano_analitica.exercicio = plano_conta.exercicio
                     AND plano_analitica.cod_conta = plano_conta.cod_conta
                     
                    JOIN contabilidade.conta_credito
                      ON plano_analitica.exercicio = conta_credito.exercicio
                     AND plano_analitica.cod_plano = conta_credito.cod_plano
                     
                    JOIN administracao.configuracao_entidade
                      ON configuracao_entidade.cod_entidade = conta_credito.cod_entidade
                     AND configuracao_entidade.exercicio = conta_credito.exercicio
                     AND configuracao_entidade.cod_modulo = 55
                     AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'


			JOIN(
                           SELECT lote.cod_lote
				 , lote.dt_lote
				 , lote.exercicio
				 , conta_credito.cod_plano
				 , lote.tipo
				 , lote.cod_entidade
				 , valor_lancamento.vl_lancamento
                          FROM  contabilidade.lote
                          JOIN contabilidade.valor_lancamento
                            ON valor_lancamento.exercicio = lote.exercicio
                           AND valor_lancamento.cod_entidade = lote.cod_entidade
                           AND valor_lancamento.tipo = lote.tipo
                           AND valor_lancamento.cod_lote = lote.cod_lote
			                 AND valor_lancamento.tipo_valor = 'C'

                          JOIN contabilidade.conta_credito
                            ON conta_credito.exercicio = valor_lancamento.exercicio
                           AND conta_credito.cod_entidade = valor_lancamento.cod_entidade
                           AND conta_credito.tipo = valor_lancamento.tipo
                           AND conta_credito.cod_lote = valor_lancamento.cod_lote
                           AND conta_credito.sequencia = valor_lancamento.sequencia
                           AND valor_lancamento.tipo = 'T'

                           Where lote.exercicio = '".$this->getDado('exercicio')."' AND lote.tipo = 'T'
                           Group by 1,2,3,4,5,6,7
                           Order By lote.cod_lote

			)as lote
                           ON lote.exercicio = plano_analitica.exercicio
                           AND lote.cod_plano = plano_analitica.cod_plano
                           AND lote.tipo = transferencia.tipo
			               AND lote.cod_entidade =  transferencia.cod_entidade 
                           AND lote.cod_lote = transferencia.cod_lote
                
                    LEFT JOIN contabilidade.plano_recurso
                         ON plano_recurso.cod_plano = lote.cod_plano
                        AND plano_recurso.exercicio = lote.exercicio 
                           
                    WHERE balancete_extmmaa.exercicio = '".$this->getDado('exercicio')."'
                     AND transferencia.cod_entidade IN (".$this->getDado('entidades').")
                    AND lote.dt_lote BETWEEN TO_DATE('".$this->getDado('dt_inicial')."', 'dd/mm/yyyy') and TO_DATE('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
                    AND transferencia.cod_tipo = 2
                    
                   UNION
                   
                    -- select para as despesas
                  SELECT
                          21 AS tipo_registro
                        , balancete_extmmaa.cod_plano AS cod_ext
                        , plano_recurso.cod_recurso AS cod_font_recurso
                        , 2 AS categoria
                        , TO_CHAR(lote.dt_lote, 'ddmmyyyy') AS dt_lancamento
                        , lote.vl_lancamento
                        
                    FROM tesouraria.transferencia
                    
                    
                    JOIN contabilidade.plano_analitica
                      ON plano_analitica.cod_plano = transferencia.cod_plano_debito
                     AND plano_analitica.exercicio = transferencia.exercicio
                     
                    JOIN tcemg.balancete_extmmaa
                      ON balancete_extmmaa.cod_plano = plano_analitica.cod_plano
                     AND balancete_extmmaa.exercicio = plano_analitica.exercicio
                     
                    JOIN contabilidade.plano_conta
                      ON plano_analitica.exercicio = plano_conta.exercicio
                     AND plano_analitica.cod_conta = plano_conta.cod_conta
                     
                    JOIN contabilidade.conta_debito
                      ON plano_analitica.exercicio = conta_debito.exercicio
                     AND plano_analitica.cod_plano = conta_debito.cod_plano
                     
                    JOIN administracao.configuracao_entidade
                      ON configuracao_entidade.cod_entidade = conta_debito.cod_entidade
                     AND configuracao_entidade.exercicio = conta_debito.exercicio
                     AND configuracao_entidade.cod_modulo = 55
                     AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'


                     JOIN(
                           SELECT lote.cod_lote
				 , lote.dt_lote
				 , lote.exercicio
				 , conta_debito.cod_plano
				 , lote.tipo
				 , lote.cod_entidade
				 , valor_lancamento.vl_lancamento
                          FROM  contabilidade.lote
                          JOIN contabilidade.valor_lancamento
                            ON valor_lancamento.exercicio = lote.exercicio
                           AND valor_lancamento.cod_entidade = lote.cod_entidade
                           AND valor_lancamento.tipo = lote.tipo
                           AND valor_lancamento.cod_lote = lote.cod_lote
			   AND valor_lancamento.tipo_valor = 'D'

                          JOIN contabilidade.conta_debito
                            ON conta_debito.exercicio = valor_lancamento.exercicio
                           AND conta_debito.cod_entidade = valor_lancamento.cod_entidade
                           AND conta_debito.tipo = valor_lancamento.tipo
                           AND conta_debito.cod_lote = valor_lancamento.cod_lote
                           AND conta_debito.sequencia = valor_lancamento.sequencia
                           AND valor_lancamento.tipo = 'T'

                           Where lote.exercicio = '".$this->getDado('exercicio')."' AND lote.tipo = 'T'
                           Group by 1,2,3,4,5,6,7
                           Order By lote.cod_lote

			)as lote
                           ON lote.exercicio = plano_analitica.exercicio
                           AND lote.cod_plano = plano_analitica.cod_plano
                           AND lote.tipo = transferencia.tipo
			              AND lote.cod_entidade =  transferencia.cod_entidade 
                           AND lote.cod_lote = transferencia.cod_lote
                           
                    LEFT JOIN contabilidade.plano_recurso
                         ON plano_recurso.cod_plano = lote.cod_plano
                        AND plano_recurso.exercicio = lote.exercicio 

                   WHERE balancete_extmmaa.exercicio = '".$this->getDado('exercicio')."'
                     AND transferencia.cod_entidade IN (".$this->getDado('entidades').")
                     AND lote.dt_lote BETWEEN TO_DATE('".$this->getDado('dt_inicial')."', 'dd/mm/yyyy') and TO_DATE('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
                    AND transferencia.cod_tipo = 1
            ) AS resultado
            
                GROUP BY tipo_registro,  cod_ext , cod_font_recurso, categoria, dt_lancamento,  cod_reduzido_mov
                
                ORDER BY cod_ext
        ";
        
        return $stSql;
    }

    public function recuperaExportacao22(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaExportacao22",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao22()
    {
        $stSql = "SELECT
                    22 AS tipo_registro
                    , LPAD( (balancete_extmmaa.cod_plano::varchar || TO_CHAR(lote.dt_lote, 'ddmmyyyy') ) ,15,'0') AS cod_reduzido_mov
                    , COALESCE(lote.vl_lancamento,0.00) AS vl_op
                    , tcemg.seq_num_op_extra(transferencia.exercicio,transferencia.cod_entidade,1,transferencia.cod_lote)::varchar AS cod_reduzido_op
		            , tcemg.seq_num_op_extra(transferencia.exercicio,transferencia.cod_entidade,1,transferencia.cod_lote)::varchar||balancete_extmmaa.cod_plano||TO_CHAR(lote.dt_lote, 'ddmmyyyy') AS num_op
                    , COALESCE(documento.nro_documento::varchar,' ') AS num_documento_credor
                    , remove_acentos(plano_conta.nom_conta) AS especificacao_op
                    , documento.tipo AS tipo_documento_credor
                    , cpfrespop.cpf AS cpf_responsavel
                    , TO_CHAR(lote.dt_lote, 'ddmmyyyy') AS dt_pagamento
                    
            FROM tesouraria.transferencia
            
            JOIN contabilidade.plano_analitica
              ON plano_analitica.cod_plano = transferencia.cod_plano_debito
             AND plano_analitica.exercicio = transferencia.exercicio
             
            JOIN(
                    SELECT lote.cod_lote
                         , lote.dt_lote
                         , lote.exercicio
                         , conta_debito.cod_plano
                         , lote.tipo
                         , lote.cod_entidade
                         , valor_lancamento.vl_lancamento
                         
                    FROM  contabilidade.lote
                    
                    JOIN contabilidade.valor_lancamento
                      ON valor_lancamento.exercicio = lote.exercicio
                     AND valor_lancamento.cod_entidade = lote.cod_entidade
                     AND valor_lancamento.tipo = lote.tipo
                     AND valor_lancamento.cod_lote = lote.cod_lote
                     AND valor_lancamento.tipo_valor = 'D'
                     
                    JOIN contabilidade.conta_debito
                      ON conta_debito.exercicio = valor_lancamento.exercicio
                     AND conta_debito.cod_entidade = valor_lancamento.cod_entidade
                     AND conta_debito.tipo = valor_lancamento.tipo
                     AND conta_debito.cod_lote = valor_lancamento.cod_lote
                     AND conta_debito.sequencia = valor_lancamento.sequencia
                     AND valor_lancamento.tipo = 'T'
                     
                    WHERE lote.exercicio = '".$this->getDado('exercicio')."' AND lote.tipo = 'T'
                      AND lote.dt_lote BETWEEN TO_DATE ('".$this->getDado('dt_inicial')."', 'dd/mm/yyyy') and TO_DATE ('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
                    GROUP BY 1,2,3,4,5,6,7
                    ORDER BY lote.cod_lote
		    ) AS lote
              ON lote.exercicio = plano_analitica.exercicio
             AND lote.cod_plano = plano_analitica.cod_plano
             AND lote.tipo = transferencia.tipo
             AND lote.cod_entidade =  transferencia.cod_entidade 
             AND lote.cod_lote = transferencia.cod_lote
             
            JOIN tcemg.balancete_extmmaa
              ON balancete_extmmaa.cod_plano = plano_analitica.cod_plano
             AND balancete_extmmaa.exercicio = plano_analitica.exercicio
             
            JOIN contabilidade.plano_conta
              ON plano_analitica.exercicio = plano_conta.exercicio
             AND plano_analitica.cod_conta = plano_conta.cod_conta
             
            JOIN contabilidade.conta_debito
              ON plano_analitica.exercicio = conta_debito.exercicio
             AND plano_analitica.cod_plano = conta_debito.cod_plano
             
            JOIN administracao.configuracao_entidade
              ON configuracao_entidade.cod_entidade = conta_debito.cod_entidade
             AND configuracao_entidade.exercicio = conta_debito.exercicio
             AND configuracao_entidade.cod_modulo = 55
             AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
             
            LEFT JOIN (SELECT cpf
                       , uniorcam.exercicio
                       
                    FROM sw_cgm_pessoa_fisica
                    
                    JOIN tcemg.uniorcam
                      ON uniorcam.cgm_ordenador = sw_cgm_pessoa_fisica.numcgm 
                     AND uniorcam.num_orgao = 02
                     AND uniorcam.num_unidade = 01
            ) AS cpfrespop
              ON cpfrespop.exercicio = balancete_extmmaa.exercicio
              
            LEFT JOIN tesouraria.recibo_extra_transferencia AS RET
                ON RET.exercicio = plano_analitica.exercicio
	           AND RET.cod_lote= transferencia.cod_lote
	           AND RET.tipo = transferencia.tipo
             
             LEFT JOIN tesouraria.recibo_extra AS RE
                ON plano_analitica.cod_plano    = RE.cod_plano
                AND plano_analitica.exercicio   = RE.exercicio
                AND RE.tipo_recibo = 'D'

            LEFT JOIN tesouraria.recibo_extra_credor AS REC
              ON REC.exercicio = RE.exercicio
             AND REC.cod_entidade= RE.cod_entidade
             AND REC.cod_recibo_extra = RE.cod_recibo_extra 
             AND REC.tipo_recibo = 'D'
             
            LEFT JOIN (SELECT   sw_cgm.numcgm
	   	                       , CASE  WHEN (sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm) THEN 1
             	                       WHEN (sw_cgm.numcgm = sw_cgm_pessoa_juridica.numcgm) THEN 2	
            	                       WHEN (sw_cgm.cod_pais != sw_pais.cod_pais) THEN 3
		                        END as tipo
                                , CASE  WHEN (sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm) THEN sw_cgm_pessoa_fisica.cpf
                   	                    WHEN (sw_cgm.numcgm = sw_cgm_pessoa_juridica.numcgm) THEN sw_cgm_pessoa_juridica.cnpj	
                   	                    WHEN (sw_cgm.cod_pais != sw_pais.cod_pais) THEN 0000000000::TEXT
		                          END AS nro_documento
                    FROM sw_cgm

                    JOIN sw_pais
                      ON sw_pais.cod_pais = sw_cgm.cod_pais
                      
	               LEFT JOIN sw_cgm_pessoa_fisica
		              ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                      
	               LEFT JOIN sw_cgm_pessoa_juridica 
		              ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
            ) AS documento
	           ON documento.numcgm = REC.numcgm
              
            WHERE balancete_extmmaa.exercicio = '".$this->getDado('exercicio')."'
            AND transferencia.cod_entidade IN (".$this->getDado('entidades').")
            AND transferencia.cod_tipo = 1
             
        GROUP BY tipo_registro, cod_reduzido_mov, vl_op, especificacao_op, cod_reduzido_op, num_op, num_documento_credor, especificacao_op, cpf_responsavel, dt_pagamento, tipo_documento_credor
        
        ORDER BY cod_reduzido_mov
        ";
        
        return $stSql;
    }

    public function recuperaExportacao23(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaExportacao23",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao23()
    {
        $stSql = "  SELECT
                            23 AS tipo_registro
                            , tcemg.seq_num_op_extra(transferencia.exercicio,transferencia.cod_entidade,1,transferencia.cod_lote)::varchar AS cod_reduzido_op
                            , CASE WHEN pagamento_tipo_documento.cod_tipo_documento IS NULL THEN
                                                '99'
                                        ELSE
                                                pagamento_tipo_documento.cod_tipo_documento
                              END AS tipo_documento_op                            
                            , pagamento_tipo_documento.num_documento AS num_documento
                            , cod_ctb_transferencia.cod_ctb_anterior as cod_ctb
                            , plano_recurso.cod_recurso AS cod_fonte_ctb
                            , CASE WHEN pagamento_tipo_documento.cod_tipo_documento = 99 OR pagamento_tipo_documento.cod_tipo_documento IS NULL THEN
                                                tipo_documento.descricao
                                        ELSE
                                                ''
                              END AS desctipodocumentoop
                            , TO_CHAR(transferencia.dt_autenticacao, 'ddmmyyyy') AS dt_emissao
                            , COALESCE(lote.vl_lancamento, 0.00) AS vl_documento
                    
                    FROM tesouraria.transferencia
            
                    JOIN contabilidade.plano_analitica
                        ON plano_analitica.cod_plano = transferencia.cod_plano_debito
                        AND plano_analitica.exercicio = transferencia.exercicio
                    
                    JOIN(   SELECT  lote.cod_lote
                                    , lote.dt_lote
                                    , lote.exercicio
                                    , conta_debito.cod_plano
                                    , lote.tipo
                                    , lote.cod_entidade
                                    , valor_lancamento.vl_lancamento
                         
                            FROM  contabilidade.lote
                    
                            JOIN contabilidade.valor_lancamento
                              ON valor_lancamento.exercicio = lote.exercicio
                             AND valor_lancamento.cod_entidade = lote.cod_entidade
                             AND valor_lancamento.tipo = lote.tipo
                             AND valor_lancamento.cod_lote = lote.cod_lote
                             AND valor_lancamento.tipo_valor = 'D'
                     
                            JOIN contabilidade.conta_debito
                              ON conta_debito.exercicio = valor_lancamento.exercicio
                             AND conta_debito.cod_entidade = valor_lancamento.cod_entidade
                             AND conta_debito.tipo = valor_lancamento.tipo
                             AND conta_debito.cod_lote = valor_lancamento.cod_lote
                             AND conta_debito.sequencia = valor_lancamento.sequencia
                             AND valor_lancamento.tipo = 'T'
                     
                            WHERE lote.exercicio = '".$this->getDado('exercicio')."' 
                            AND lote.tipo = 'T'
                            AND lote.dt_lote BETWEEN TO_DATE('".$this->getDado('dt_inicial')."', 'dd/mm/yyyy') and TO_DATE('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
                            GROUP BY 1,2,3,4,5,6,7
                            ORDER BY lote.cod_lote
		            ) AS lote
                        ON lote.exercicio = plano_analitica.exercicio
                        AND lote.cod_plano = plano_analitica.cod_plano
                        AND lote.tipo = transferencia.tipo
                        AND lote.cod_entidade =  transferencia.cod_entidade 
                        AND lote.cod_lote = transferencia.cod_lote
             
                    JOIN tcemg.balancete_extmmaa
                        ON balancete_extmmaa.cod_plano = plano_analitica.cod_plano
                        AND balancete_extmmaa.exercicio = plano_analitica.exercicio
                        
                    LEFT JOIN tesouraria.pagamento
                        ON pagamento.cod_plano        = plano_analitica.cod_plano
                        AND pagamento.exercicio_plano = plano_analitica.exercicio

                    LEFT JOIN tcemg.pagamento_tipo_documento
                        ON pagamento_tipo_documento.cod_entidade = pagamento.cod_entidade
                        AND pagamento_tipo_documento.exercicio   = pagamento.exercicio
                        AND pagamento_tipo_documento.timestamp   = pagamento.timestamp
                        AND pagamento_tipo_documento.cod_nota    = pagamento.cod_nota
             
                    LEFT JOIN tcemg.tipo_documento
                        ON tipo_documento.cod_tipo = pagamento_tipo_documento.cod_tipo_documento

                    JOIN contabilidade.plano_conta
                        ON plano_analitica.exercicio = plano_conta.exercicio
                        AND plano_analitica.cod_conta = plano_conta.cod_conta
             
                    JOIN contabilidade.conta_debito
                        ON plano_analitica.exercicio = conta_debito.exercicio
                        AND plano_analitica.cod_plano = conta_debito.cod_plano

	                LEFT JOIN contabilidade.plano_recurso
                        ON plano_recurso.exercicio = balancete_extmmaa.exercicio
                        AND plano_recurso.cod_plano = balancete_extmmaa.cod_plano
                
                    LEFT JOIN (
                                SELECT conta_debito.cod_lote
			                         , conta_debito.tipo
			                         , conta_debito.exercicio
			                         , conta_debito.cod_entidade
			                         , conta_bancaria.cod_ctb_anterior
			                         , transferencia.cod_plano_credito
			                         , transferencia.cod_plano_debito
			                         , conta_debito.sequencia
                                FROM contabilidade.conta_debito
                                
                                INNER JOIN contabilidade.lote AS lo 
                                    ON conta_debito.cod_lote     = lo.cod_lote
		                            AND conta_debito.tipo         = lo.tipo
		                            AND conta_debito.exercicio    = lo.exercicio
		                            AND conta_debito.cod_entidade = lo.cod_entidade
                                
                                INNER JOIN tesouraria.transferencia
			                        ON transferencia.cod_plano_debito = conta_debito.cod_plano
		                            AND lo.cod_lote = transferencia.cod_lote
		                            AND transferencia.cod_entidade = lo.cod_entidade
		                            AND transferencia.tipo = 'T'
		                            AND transferencia.cod_tipo = 1
                
                                INNER JOIN contabilidade.plano_analitica
                                    ON plano_analitica.cod_plano = transferencia.cod_plano_credito
                                    AND plano_analitica.natureza_saldo = 'D'
                                    AND plano_analitica.exercicio = conta_debito.exercicio
                                
                                LEFT JOIN tcemg.conta_bancaria
			                        ON conta_bancaria.cod_conta = plano_analitica.cod_conta
		                            AND conta_bancaria.exercicio = plano_analitica.exercicio
                     
                                WHERE conta_debito.exercicio = '".$this->getDado('exercicio')."'
                                AND conta_debito.cod_entidade IN (2)
                                AND  lo.dt_lote BETWEEN TO_DATE('".$this->getDado('dt_inicial')."', 'dd/mm/yyyy') and TO_DATE('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
                                AND  lo.exercicio = '".$this->getDado('exercicio')."'
		                        AND conta_debito.tipo = 'T'
                    ) AS cod_ctb_transferencia
                        ON cod_ctb_transferencia.exercicio = conta_debito.exercicio                             
                        AND cod_ctb_transferencia.sequencia = conta_debito.sequencia
                        AND cod_ctb_transferencia.cod_lote = transferencia.cod_lote
                        AND cod_ctb_transferencia.tipo =conta_debito.tipo
                        AND cod_ctb_transferencia.cod_plano_debito = conta_debito.cod_plano
	     
                    WHERE balancete_extmmaa.exercicio = '".$this->getDado('exercicio')."'
                    AND transferencia.cod_entidade IN (".$this->getDado('entidades').")
                    AND transferencia.cod_tipo = 1
                
                    GROUP BY tipo_registro
                            , tipo_documento_op
                            , num_documento
                            , cod_reduzido_op
                            , cod_ctb
                            , cod_fonte_ctb
                            , desctipodocumentoop
                            , dt_emissao
                            , vl_documento
        
                    ORDER BY cod_reduzido_op
        ";        
        return $stSql;
    }
    
    public function recuperaExportacao24(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaExportacao24",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao24()
    {
        $stSql = "
                SELECT
                        24 AS tipo_registro
                      , tcemg.seq_num_op_extra(transferencia.exercicio,transferencia.cod_entidade,1,transferencia.cod_lote)::varchar AS cod_reduzido_op
                      , LPAD(balancete_extmmaa.tipo_lancamento::varchar,4,'0') AS tipo_retencao
                      , CASE WHEN balancete_extmmaa.tipo_lancamento <> 1
                              AND balancete_extmmaa.tipo_lancamento <> 2
                              AND balancete_extmmaa.tipo_lancamento <> 3
                              AND balancete_extmmaa.tipo_lancamento <> 4
                             THEN remove_acentos(plano_conta.nom_conta)
                             ELSE ' '
                      END AS descricao_retencao
                      , COALESCE(lote.vl_lancamento,0.00) AS vl_retencao
                      
                FROM tesouraria.transferencia
                
                JOIN contabilidade.plano_analitica
                  ON plano_analitica.cod_plano = transferencia.cod_plano_debito
                 AND plano_analitica.exercicio = transferencia.exercicio
                 
		JOIN(
                    SELECT lote.cod_lote
                         , lote.dt_lote
                         , lote.exercicio
                         , conta_debito.cod_plano
                         , lote.tipo
                         , lote.cod_entidade
                         , valor_lancamento.vl_lancamento
                         
                    FROM  contabilidade.lote
                    
                    JOIN contabilidade.valor_lancamento
                      ON valor_lancamento.exercicio = lote.exercicio
                     AND valor_lancamento.cod_entidade = lote.cod_entidade
                     AND valor_lancamento.tipo = lote.tipo
                     AND valor_lancamento.cod_lote = lote.cod_lote
                     AND valor_lancamento.tipo_valor = 'D'
                     
                    JOIN contabilidade.conta_debito
                      ON conta_debito.exercicio = valor_lancamento.exercicio
                     AND conta_debito.cod_entidade = valor_lancamento.cod_entidade
                     AND conta_debito.tipo = valor_lancamento.tipo
                     AND conta_debito.cod_lote = valor_lancamento.cod_lote
                     AND conta_debito.sequencia = valor_lancamento.sequencia
                     AND valor_lancamento.tipo = 'T'
                     
                    WHERE lote.exercicio = '2014' AND lote.tipo = 'T'
                      AND lote.dt_lote BETWEEN TO_DATE('".$this->getDado('dt_inicial')."', 'dd/mm/yyyy') and TO_DATE('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
                    GROUP BY 1,2,3,4,5,6,7
                    ORDER BY lote.cod_lote
		) AS lote
              ON lote.exercicio = plano_analitica.exercicio
             AND lote.cod_plano = plano_analitica.cod_plano
             AND lote.tipo = transferencia.tipo
             AND lote.cod_entidade =  transferencia.cod_entidade 
             AND lote.cod_lote = transferencia.cod_lote
             
            JOIN tcemg.balancete_extmmaa
              ON balancete_extmmaa.cod_plano = plano_analitica.cod_plano
             AND balancete_extmmaa.exercicio = plano_analitica.exercicio
             
            JOIN contabilidade.plano_conta
              ON plano_analitica.exercicio = plano_conta.exercicio
             AND plano_analitica.cod_conta = plano_conta.cod_conta
             
            JOIN contabilidade.conta_debito
              ON plano_analitica.exercicio = conta_debito.exercicio
             AND plano_analitica.cod_plano = conta_debito.cod_plano
             
	    JOIN contabilidade.plano_recurso
                    ON plano_recurso.exercicio = balancete_extmmaa.exercicio
                   AND plano_recurso.cod_plano = balancete_extmmaa.cod_plano
                      
               WHERE balancete_extmmaa.exercicio =  '".$this->getDado( 'exercicio' ). "'
		 AND balancete_extmmaa.tipo_lancamento = 1
		 AND transferencia.cod_entidade IN (".$this->getDado('entidades').")
            
            GROUP BY 1,2,3,4, 5
            ORDER BY cod_reduzido_op
        
        /*
                SELECT 
                        24 AS tipo_registro
                      , codigo_op.codigo AS cod_reduzido_op
                      , transferencia_ordem_pagamento_retencao.tipo AS tipo_retencao
                      , remove_acentos(plano_conta.nom_conta) AS descricao_retencao
                      , SUM(transferencia.valor) AS vl_retencao
                      
                FROM tesouraria.transferencia_ordem_pagamento_retencao

                JOIN tesouraria.transferencia
                  ON transferencia_ordem_pagamento_retencao.cod_lote = transferencia.cod_lote
                 AND transferencia_ordem_pagamento_retencao.cod_entidade = transferencia.cod_entidade
                 AND transferencia_ordem_pagamento_retencao.exercicio = transferencia.exercicio
                 AND transferencia.tipo = 'T'
            
                JOIN (SELECT cod_lote
                           , transferencia.cod_entidade
                           , tcemg.seq_num_op_extra(exercicio,cod_entidade,cod_tipo,cod_lote) AS codigo
                        FROM tesouraria.transferencia 
                       WHERE exercicio='2014' 
                         AND cod_entidade=transferencia.cod_entidade
                         AND cod_tipo=2 
                         AND cod_lote = transferencia.cod_lote
                   )  AS codigo_op
                  ON codigo_op.cod_lote = transferencia_ordem_pagamento_retencao.cod_lote
                 AND codigo_op.cod_entidade = transferencia.cod_entidade
                  
                JOIN contabilidade.plano_analitica
                  ON plano_analitica.cod_plano = transferencia_ordem_pagamento_retencao.cod_plano
                 AND plano_analitica.exercicio = transferencia_ordem_pagamento_retencao.exercicio
                 
                JOIN tcemg.balancete_extmmaa
                  ON balancete_extmmaa.cod_plano = transferencia_ordem_pagamento_retencao.cod_plano
                 AND balancete_extmmaa.exercicio = transferencia_ordem_pagamento_retencao.exercicio
                   
                JOIN contabilidade.plano_conta
                  ON plano_analitica.exercicio = plano_conta.exercicio
                 AND plano_analitica.cod_conta = plano_conta.cod_conta
                      
               WHERE balancete_extmmaa.exercicio      =  '".$this->getDado( 'exercicio' ). "'
                 AND transferencia.dt_autenticacao BETWEEN TO_DATE('".$this->getDado('dt_inicial')."', 'dd/mm/yyyy') and TO_DATE('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
            
            GROUP BY 1,2,3,4
            ORDER BY cod_reduzido_op
        */
        ";
        
        return $stSql;
    }
	
	public function __destruct(){}

}
?>
