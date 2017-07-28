<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos (urbem@cnm.org.br)      *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo  sob *
    * os termos da Licença Pública Geral GNU conforme publicada pela  Free  Software *
    * Foundation; tanto a versão 2 da Licença, como (a seu critério) qualquer versão *
    * posterior.                                                                     *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral  do  GNU  junto  com *
    * este programa; se não, escreva para  a  Free  Software  Foundation,  Inc.,  no *
    * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.               *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
  * Página de Include Oculta - Exportação Arquivos TCEMG - OBELAC.csv
  * Data de Criação: 29/08/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: OBELAC.csv.inc.php 59820 2014-09-12 18:17:20Z luciana $
  * $Date: 2014-09-12 15:17:20 -0300 (Fri, 12 Sep 2014) $
  * $Author: luciana $
  * $Rev: 59820 $
  *
*/
/**
* OBELAC.csv | Autor : Carlos Adriano
*/

//Tipo Registro 99
$arRecordSet = array(
    '0' => array(
        'tipo_registro' => '99',
    )
);

$rsRecordSet[$stNomeArquivo] = new RecordSet();
$rsRecordSet[$stNomeArquivo]->preenche($arRecordSet);
            
$obExportador->roUltimoArquivo->addBloco($rsRecordSet[$stNomeArquivo]);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
$obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

?>