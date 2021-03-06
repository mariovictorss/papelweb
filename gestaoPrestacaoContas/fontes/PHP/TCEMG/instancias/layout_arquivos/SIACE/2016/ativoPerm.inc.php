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

 /*
    * Arquivo de geracao do arquivo sertTerceiros TCM/MG
    * Data de Criação   : 04/02/2009

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Lucas Andrades Mendes

    * @package URBEM
    * @subpackage

    * @ignore

    $Id: ativoPerm.inc.php 62754 2015-06-16 17:17:35Z evandro $
    */

    include_once( CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio().'/FTCEMGAtivoPerm.class.php');

    $arFiltros = Sessao::read('filtroRelatorio');

    SistemaLegado::retornaInicialFinalMesesPeriodicidade( $arDatas,"ano",$arFiltros['inPeriodo'],Sessao::getExercicio() );

    foreach ($arDatas as $stDatas) {

        $obFTCEMGAtivoPerm = new FTCEMGAtivoPerm();
        $obFTCEMGAtivoPerm->setDado('exercicio'    , Sessao::getExercicio() );
        $obFTCEMGAtivoPerm->setDado('cod_entidade' , implode(',',$arFiltros['inCodEntidadeSelecionado']) );
        $obFTCEMGAtivoPerm->setDado('data_inicial' , $stDatas['stDtInicial'] );
        $obFTCEMGAtivoPerm->setDado('data_final'   , $stDatas['stDtFinal'] );

        $obFTCEMGAtivoPerm->recuperaTodos($rsDespesaCorrente);

        $obExportador->roUltimoArquivo->addBloco($rsDespesaCorrente);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('mes');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('valorbensmov');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('valorbensimo');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('valorobrasinst');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('valortitval');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('valordivativa');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
       
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('valortransrecebidas');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
       
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('valorreversaorpps');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('codtipo');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    }

?>
