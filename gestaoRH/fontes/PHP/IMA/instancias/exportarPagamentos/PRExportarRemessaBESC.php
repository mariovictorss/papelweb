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
    * Página de Processamento do Exportação Remessa BESC
    * Data de Criação: 04/12/2006

    * @author Desenvolvedor: Alex Cardoso

    * Casos de uso: uc-04.08.08

    $Id: PRExportarRemessaBESC.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CLA_EXPORTADOR                  );

$stAcao = $_REQUEST["stAcao"] ? $_REQUEST["stAcao"] : $_GET["stAcao"];
$arSessaoLink = Sessao::read('link');
$stLink = "&pg=".$arSessaoLink["pg"]."&pos=".$arSessaoLink["pos"];

foreach ($_POST as $key=>$value) {
    $stLink .= $key."=".$value."&";
}

//Define o nome dos arquivos PHP
$stPrograma = "ExportarRemessaBESC";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

Sessao::setTrataExcecao(true);

include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoConvenioBesc.class.php");
$obTIMAConfiguracaoConvenioBesc = new TIMAConfiguracaoConvenioBesc();
$obTIMAConfiguracaoConvenioBesc->recuperaTodos($rsConfiguracaoConvenio);

$rsContasConvenio = Sessao::read("rsContas");

include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoBescOrgao.class.php");
$obTIMAConfiguracaoBescOrgao = new TIMAConfiguracaoBescOrgao;

include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoBescLocal.class.php");
$obTIMAConfiguracaoBescLocal = new TIMAConfiguracaoBescLocal;

$obExportador = new Exportador();
$obExportador->setRetorno($pgForm);
$arArquivoDadosExtras = array();
$inNumeroSequencial = $_POST["inNumeroSequencial"];

while (!$rsContasConvenio->eof()) {

        //Busca os orgões cadastrados na configuração de cada conta individualmente
        //esses códigos devem ser incluídos no filtro fixo junto com o filtro de banco
        $obTIMAConfiguracaoBescOrgao->setDado("cod_convenio"      , $rsContasConvenio->getCampo("cod_convenio"));
        $obTIMAConfiguracaoBescOrgao->setDado("cod_agencia"       , $rsContasConvenio->getCampo("cod_agencia"));
        $obTIMAConfiguracaoBescOrgao->setDado("cod_banco"         , $rsContasConvenio->getCampo("cod_banco"));
        $obTIMAConfiguracaoBescOrgao->setDado("cod_conta_corrente", $rsContasConvenio->getCampo("cod_conta_corrente"));
        $obTIMAConfiguracaoBescOrgao->setDado("timestamp"         , $rsContasConvenio->getCampo("timestamp"));
        $obTIMAConfiguracaoBescOrgao->recuperaPorChave($rsOrgaos);
        $stCodOrgaos = "";
        while (!$rsOrgaos->eof()) {
            $stCodOrgaos .= $rsOrgaos->getCampo("cod_orgao").",";
            $rsOrgaos->proximo();
        }
        $stCodOrgaos = substr($stCodOrgaos,0,strlen($stCodOrgaos)-1);

        $obTIMAConfiguracaoBescLocal->setDado("cod_convenio"      , $rsContasConvenio->getCampo("cod_convenio"));
        $obTIMAConfiguracaoBescLocal->setDado("cod_agencia"       , $rsContasConvenio->getCampo("cod_agencia"));
        $obTIMAConfiguracaoBescLocal->setDado("cod_banco"         , $rsContasConvenio->getCampo("cod_banco"));
        $obTIMAConfiguracaoBescLocal->setDado("cod_conta_corrente", $rsContasConvenio->getCampo("cod_conta_corrente"));
        $obTIMAConfiguracaoBescLocal->setDado("timestamp"         , $rsContasConvenio->getCampo("timestamp"));
        $obTIMAConfiguracaoBescLocal->recuperaPorChave($rsLocais);
        $stCodLocais = "";
        while (!$rsLocais->eof()) {
            $stCodLocais .= $rsLocais->getCampo("cod_local").",";
            $rsLocais->proximo();
        }
        if (strlen($stCodLocais) > 0) {
            $stCodLocais = substr($stCodLocais,0,strlen($stCodLocais)-1);
        }

        ################################## BANCO CONVENIO #################################

        $arAgenciaConvenio              = separarDigito($rsContasConvenio->getCampo("num_agencia"));
        $inDigitoVerificadorAgencia     = $arAgenciaConvenio[1];
        $inAgenciaConvenio              = $arAgenciaConvenio[0];
        $arContaConvenio                = separarDigito($rsContasConvenio->getCampo("num_conta_corrente"));
        $inDigitoVerificadorConta       = $arContaConvenio[1];
        $inContaConvenio                = $arContaConvenio[0];

        ################################## EXPORTADOR ###################################

        // Criando objeto que monta arquivo de remessa
        $arConta = separarDigito($rsContasConvenio->getCampo("num_conta_corrente"));
        $stNomeArquivo = "AE".$rsConfiguracaoConvenio->getCampo("cod_convenio_banco")."_".$rsContasConvenio->getCampo("num_conta_corrente").".AGL";
        $arArquivoDadosExtras[$stNomeArquivo]['descricao'] = $rsContasConvenio->getCampo("descricao");
        $arArquivoDadosExtras[$stNomeArquivo]['seq']       = $inNumeroSequencial;

        $obExportador->addArquivo($stNomeArquivo);
        $obExportador->roUltimoArquivo->setTipoDocumento("RemessaBESC");

        ################################## COMPETENCIA ###################################

        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $stCompetencia  = (  $_POST["inCodMes"] < 10 ) ? "0".$_POST["inCodMes"] : $_POST["inCodMes"];
        $stCompetencia .= $_POST["inAno"];
        $stFiltroCompetencia = " WHERE to_char(dt_final,'mmyyyy') = '".$stCompetencia."'";
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaTodos($rsPeriodoMovimentacao,$stFiltroCompetencia);

        $arInicialCompetenciaSelecionada = explode("/",$rsPeriodoMovimentacao->getCampo("dt_inicial"));
        $arFinalCompetenciaSelecionada = explode("/",$rsPeriodoMovimentacao->getCampo("dt_final"));

        $dtInicialCompetenciaSelecionada = $arInicialCompetenciaSelecionada[2]."-".$arInicialCompetenciaSelecionada[1]."-".$arInicialCompetenciaSelecionada[0];
        $dtFinalCompetenciaSelecionada = $arFinalCompetenciaSelecionada[2]."-".$arFinalCompetenciaSelecionada[1]."-".$arFinalCompetenciaSelecionada[0];

        ///////// COMPETENCIA ANTERIOR SELECIONADA
        $inCodMovimentacaoAnteriorSelecionada = $rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao') - 1;
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacao($rsPenultimaMovimentacao, " AND FPM.cod_periodo_movimentacao = ".$inCodMovimentacaoAnteriorSelecionada);

        if ($rsPenultimaMovimentacao->getCampo('dt_inicial') != "") {
            $arInicialCompetenciaAnteriorSelecionada = explode("/",$rsPenultimaMovimentacao->getCampo('dt_inicial'));
            $arFinalCompetenciaAnteriorSelecionada   = explode("/",$rsPenultimaMovimentacao->getCampo('dt_final'));
        } else {
            $arInicialCompetenciaAnteriorSelecionada = $arInicialCompetenciaSelecionada;
            $arFinalCompetenciaAnteriorSelecionada   = $arFinalCompetenciaSelecionada;
        }

        $dtInicialCompetenciaAnteriorSelecionada = $arInicialCompetenciaAnteriorSelecionada[2]."-".$arInicialCompetenciaAnteriorSelecionada[1]."-".$arInicialCompetenciaAnteriorSelecionada[0];
        $dtFinalCompetenciaAnteriorSelecionada   = $arFinalCompetenciaAnteriorSelecionada[2]."-".$arFinalCompetenciaAnteriorSelecionada[1]."-".$arFinalCompetenciaAnteriorSelecionada[0];

        ///////// COMPETENCIA ATUAL
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsUltimaMovimentacao);

        $arInicialCompetenciaAtual = explode("/",$rsUltimaMovimentacao->getCampo('dt_inicial'));
        $arFinalCompetenciaAtual   = explode("/",$rsUltimaMovimentacao->getCampo('dt_final'));

        $dtInicialCompetenciaAtual = $arInicialCompetenciaAtual[2]."-".$arInicialCompetenciaAtual[1]."-".$arInicialCompetenciaAtual[0];
        $dtFinalCompetenciaAtual   = $arFinalCompetenciaAtual[2]."-".$arFinalCompetenciaAtual[1]."-".$arFinalCompetenciaAtual[0];

        #############################################################################

        if ($_POST['nuValorLiquidoInicial'] != "" && $_POST['nuValorLiquidoFinal'] != "") {
            $nuValorLiquidoFinal = str_replace(".","",$_POST["nuValorLiquidoFinal"]);
            $nuValorLiquidoFinal = str_replace(",",".",$nuValorLiquidoFinal);

            $nuValorLiquidoInicial = str_replace(".","",$_POST["nuValorLiquidoInicial"]);
            $nuValorLiquidoInicial = str_replace(",",".",$nuValorLiquidoInicial);
        }

        if ($_POST["nuPercentualPagar"] != "") {
            $nuPercentualPagar = str_replace(".", "",  $_POST["nuPercentualPagar"]);
            $nuPercentualPagar = str_replace(",", ".", $nuPercentualPagar);
        } else {
            $nuPercentualPagar = 0;
        }

        ################################## ATIVOS/APOSENTADOS/PENSIONISTA ###################################
        $stFiltroContrato  = "\n AND cod_orgao IN (".$stCodOrgaos.")";
        if (trim($stCodLocais) != "") {
            $stFiltroContrato .= "\n AND cod_local IN (".$stCodLocais.")";
        }

        if ($_POST['stSituacao'] == 'ativos' ||
           $_POST['stSituacao'] == 'aposentados' ||
           $_POST['stSituacao'] == 'rescindidos' ||
           $_POST['stSituacao'] == 'pensionistas' ||
           $_POST['stSituacao'] == 'todos') {

            $stValoresFiltro = "";
            switch ($_POST['stTipoFiltro']) {
                case "contrato":
                case "contrato_rescisao":
                case "contrato_aposentado":
                case "contrato_todos":
                case "cgm_contrato":
                case "cgm_contrato_rescisao":
                case "cgm_contrato_aposentado":
                case "cgm_contrato_todos":
                    $arContratos = Sessao::read("arContratos");
                    foreach ($arContratos as $arContrato) {
                        $stValoresFiltro .= $arContrato["cod_contrato"].",";
                    }
                    $stValoresFiltro = substr($stValoresFiltro,0,strlen($stValoresFiltro)-1);
                    break;
                case "contrato_pensionista":
                case "cgm_contrato_pensionista":
                    $arPensionistas = Sessao::read("arPensionistas");
                    foreach ($arPensionistas as $arPensionista) {
                        $stValoresFiltro .= $arPensionista["cod_contrato"].",";
                    }
                    $stValoresFiltro = substr($stValoresFiltro,0,strlen($stValoresFiltro)-1);
                    break;
                case "lotacao":
                    $stValoresFiltro = implode(",",$_REQUEST["inCodLotacaoSelecionados"]);
                    break;
                case "local":
                    $stValoresFiltro = implode(",",$_REQUEST["inCodLocalSelecionados"]);
                    break;
                case "atributo_servidor":
                    $inCodAtributo = $_REQUEST["inCodAtributo"];
                    $inCodCadastro = $_REQUEST["inCodCadastro"];
                    $stNomeAtributo = "Atributo_".$inCodAtributo."_".$inCodCadastro;
                    if (is_array($_REQUEST[$stNomeAtributo."_Selecionados"])) {
                        $inArray = 1;
                        $stValores     = implode(",",$_REQUEST[$stNomeAtributo."_Selecionados"]);
                    } else {
                        $inArray = 0;
                        $stValores     = $_REQUEST[$stNomeAtributo];
                    }
                    $stValoresFiltro = $inArray."#".$inCodAtributo."#".$stValores;
                    break;
                case "atributo_pensionista":
                    $inCodAtributo = $_REQUEST["inCodAtributo"];
                    $inCodCadastro = $_REQUEST["inCodCadastro"];
                    $stNomeAtributo = "Atributo_".$inCodAtributo."_".$inCodCadastro;
                    if (is_array($_REQUEST[$stNomeAtributo."_Selecionados"])) {
                        $inArray = 1;
                        $stValores = implode(",",$_REQUEST[$stNomeAtributo."_Selecionados"]);
                    } else {
                        $inArray = 0;
                        $stValores = $_REQUEST[$stNomeAtributo];
                    }
                    $stValoresFiltro = $inArray."#".$inCodAtributo."#".$stValores;
                    break;
            }
        }

        ################################## ESTAGIARIOS ###################################
        $stFiltroEstagiario  = " AND cod_orgao IN (".$stCodOrgaos.")";
        if (trim($stCodLocais) != "") {
            $stFiltroEstagiario .= " AND cod_local IN (".$stCodLocais.")";
        }

        if ($_POST['stSituacao'] == 'estagiarios' ||
           $_POST['stSituacao'] == 'todos') {
            switch ($_POST['stTipoFiltro']) {
                case "cgm_codigo_estagio":
                    foreach (Sessao::read('arEstagios') as $arEstagio) {
                        $stCodEstagio .= $arEstagio["inCodigoEstagio"].",";
                    }
                    $stCodEstagio = substr($stCodEstagio,0,strlen($stCodEstagio)-1);
                    $stFiltroEstagiario  .= " AND numero_estagio IN (".$stCodEstagio.")";
                    break;
                case "lotacao":
                    $stCodOrgao = implode(",",$_REQUEST["inCodLotacaoSelecionados"]);
                    $stFiltroEstagiario  .= " AND cod_orgao in (".$stCodOrgao.")";
                    break;
                case "local":
                    $stCodLocal = implode(",",$_POST['inCodLocalSelecionados']);
                    $stFiltroEstagiario .= " AND cod_local in (".$stCodLocal.")";
                    break;
                case "atributo_estagiario":
                    $inCodAtributo = $_REQUEST["inCodAtributo"];
                    $inCodCadastro = $_REQUEST["inCodCadastro"];
                    $stNomeAtributo = "Atributo_".$inCodAtributo."_".$inCodCadastro;
                    if (is_array($_REQUEST[$stNomeAtributo."_Selecionados"])) {
                        $inArray = 1;
                        $stValores = implode(",",$_REQUEST[$stNomeAtributo."_Selecionados"]);
                    } else {
                        $inArray = 0;
                        $stValores = $_REQUEST[$stNomeAtributo];
                    }
                    $stValoresFiltro = $inArray."#".$inCodAtributo."#".$stValores;
                    break;
            }
        }

        ################################## PENSAO JUDICIAL ###################################
        $stFiltroPensaoJudicial  = " AND cod_orgao IN (".$stCodOrgaos.")";
        if (trim($stCodLocais) != "") {
            $stFiltroPensaoJudicial .= " AND cod_local IN (".$stCodLocais.")";
        }

        //Tipo de Cadastro
        if ($_POST['stSituacao'] == 'todos' ||
           $_POST['stSituacao'] == 'pensao_judicial') {

            switch ($_POST['stTipoFiltro']) {
                case "cgm_dependente": //IFiltroComponentesDependentes
                        foreach (Sessao::read('arCGMDependentes') as $arCGMDependente) {
                            $stCGMDependente .= "'".addslashes($arCGMDependente["numcgm"])."',";
                        }
                        $stCGMDependente = substr($stCGMDependente,0,strlen($stCGMDependente)-1);

                        $stFiltroPensaoJudicial  .= " AND contrato.numcgm_dependente IN (".$stCGMDependente.")";
                        break;
                case "cgm_servidor_dependente": //IFiltroComponentesDependentes
                        foreach (Sessao::read('arContratos') as $arContrato) {
                            $stCodContrato .= $arContrato["cod_contrato"].",";
                        }
                        $stCodContrato = substr($stCodContrato,0,strlen($stCodContrato)-1);
                        $stFiltroPensaoJudicial  .= " AND cod_contrato IN (".$stCodContrato.")";
                        break;
                case "lotacao":
                        $stCodOrgao = implode(",",$_REQUEST["inCodLotacaoSelecionados"]);
                        $stFiltroPensaoJudicial  .= " AND cod_orgao in (".$stCodOrgao.")";
                        break;
            }
        }//
        #############################################################################

        $rsContrato = new RecordSet();
        if ($_POST["stSituacao"] == 'ativos' ||
            $_POST["stSituacao"] == 'aposentados' ||
            $_POST["stSituacao"] == 'rescindidos' ||
            $_POST["stSituacao"] == 'pensionistas' ||
            $_POST["stSituacao"] == 'todos') {

            include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php" );
            $obRecuperaEventoCalculado = new TFolhaPagamentoEventoCalculado();

            $obRecuperaEventoCalculado->setDado("inCodPeriodoMovimentacao", $rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
            $obRecuperaEventoCalculado->setDado("stSituacao"              , $_POST["stSituacao"]);
            $obRecuperaEventoCalculado->setDado("inCodConfiguracao"       , $_POST["inCodConfiguracao"]);
            $obRecuperaEventoCalculado->setDado("inCodComplementar"       , ($_POST["inCodComplementar"]==""?0:$_POST["inCodComplementar"]));
            $obRecuperaEventoCalculado->setDado("stTipoFiltro"            , $_POST["stTipoFiltro"]);
            $obRecuperaEventoCalculado->setDado("stValoresFiltro"         , $stValoresFiltro);
            $obRecuperaEventoCalculado->setDado("stDesdobramento"         , $_POST["stDesdobramento"]);
            $obRecuperaEventoCalculado->setDado("inCodBanco"              , $rsConfiguracaoConvenio->getCampo("cod_banco"));
            $obRecuperaEventoCalculado->setDado("nuLiquidoMinimo"         , $nuValorLiquidoInicial);
            $obRecuperaEventoCalculado->setDado("nuLiquidoMaximo"         , $nuValorLiquidoFinal);
            $obRecuperaEventoCalculado->setDado("nuPercentualPagar"       , $nuPercentualPagar);
            $obRecuperaEventoCalculado->recuperaContratosCalculadosRemessaBancos($rsContrato,$stFiltroContrato);
        }

        $rsEstagio = new RecordSet();
        if ($_POST["stSituacao"] == 'estagiarios' ||
            $_POST["stSituacao"] == 'todos') {
            include_once(CAM_GRH_EST_MAPEAMENTO."TEstagioEstagiarioEstagio.class.php");
            $obTEstagioEstagiorioEstagio = new TEstagioEstagiarioEstagio();
            $obTEstagioEstagiorioEstagio->setDado("inCodPeriodoMovimentacao", $rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao"));
            $obTEstagioEstagiorioEstagio->setDado("inCodBanco"              , $rsConfiguracaoConvenio->getCampo("cod_banco"));
            $obTEstagioEstagiorioEstagio->setDado("stTipoFiltro"            , $_POST["stTipoFiltro"]);
            $obTEstagioEstagiorioEstagio->setDado("stValoresFiltro"         , $stValoresFiltro);
            $obTEstagioEstagiorioEstagio->setDado("nuLiquidoMinimo"         , $nuValorLiquidoInicial);
            $obTEstagioEstagiorioEstagio->setDado("nuLiquidoMaximo"         , $nuValorLiquidoFinal);
            $obTEstagioEstagiorioEstagio->setDado("nuPercentualPagar"       , $nuPercentualPagar);
            $obTEstagioEstagiorioEstagio->recuperaRemessaBancos($rsEstagio,$stFiltroEstagiario);
        }

        $rsPensaoJudicial = new RecordSet();
        if ($_POST["stSituacao"] == 'todos' ||
            $_POST["stSituacao"] == 'pensao_judicial') {

            include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculadoDependente.class.php" );
            $obTFolhaPagamentoEventoCalculadoDependente = new TFolhaPagamentoEventoCalculadoDependente();
            $obTFolhaPagamentoEventoCalculadoDependente->setDado("inCodPeriodoMovimentacao", $rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
            $obTFolhaPagamentoEventoCalculadoDependente->setDado("inCodConfiguracao"       , $_POST["inCodConfiguracao"]);
            $obTFolhaPagamentoEventoCalculadoDependente->setDado("inCodComplementar"       , ($_POST["inCodComplementar"]==""?0:$_POST["inCodComplementar"]));
            $obTFolhaPagamentoEventoCalculadoDependente->setDado("stDesdobramento"         , $_POST["stDesdobramento"]);
            $obTFolhaPagamentoEventoCalculadoDependente->setDado("inCodBanco"              , $rsConfiguracaoConvenio->getCampo("cod_banco"));
            $obTFolhaPagamentoEventoCalculadoDependente->setDado("nuLiquidoMinimo"         , $nuValorLiquidoInicial);
            $obTFolhaPagamentoEventoCalculadoDependente->setDado("nuLiquidoMaximo"         , $nuValorLiquidoFinal);
            $obTFolhaPagamentoEventoCalculadoDependente->setDado("nuPercentualPagar"       , $nuPercentualPagar);
            $obTFolhaPagamentoEventoCalculadoDependente->recuperaContratosCalculadosRemessaBancos($rsPensaoJudicial,$stFiltroPensaoJudicial);
        }

        #############################################################################

        $arExportador = array();
        $inIndex = 0;
        $nuVlrLancamentoTotal = 0;
        $inQuantLotes = 0;

        $inQuantLotes += ($rsContrato->getNumLinhas() > 0) ? $rsContrato->getNumLinhas() : 0;
        while (!$rsContrato->eof()) {
            $nuVlrLancamento = $rsContrato->getCampo("proventos")-$rsContrato->getCampo("descontos");
            if ($nuVlrLancamento > 0) {
            $arExportador[$inIndex]["banco"]                         = 27;
            $arExportador[$inIndex]["lote"]                          = 1;
            $arExportador[$inIndex]["registro_detalhe_lote"]         = 3;
            $arExportador[$inIndex]["numero_sequencial"]             = $inIndex+1;
            $arExportador[$inIndex]["segmento"]                      = "A";
            $arExportador[$inIndex]["tipo_movimento"]                = $_POST["inTipoMovimento"];
            $arExportador[$inIndex]["codigo_instrucao"]              = $_POST["inInstrucaoMovimento"];
            $arExportador[$inIndex]["compensacao"]  				 = 18;
            $arExportador[$inIndex]["numero_banco_favorecido"]       = $rsContrato->getCampo("num_banco");
            $arAgencia = separarDigito($rsContrato->getCampo("num_agencia"));
            $arExportador[$inIndex]["numero_agencia"]                = $arAgencia[0];
            $arExportador[$inIndex]["digito_agencia"]                = $arAgencia[1];
            $arConta = separarDigito($rsContrato->getCampo("nr_conta"));
            $arExportador[$inIndex]["nr_conta"]                      = $arConta[0];
            $arExportador[$inIndex]["digito_conta"]                  = $arConta[1];
            $arExportador[$inIndex]["digito_agencia_conta"]          = $arConta[1];
            $arExportador[$inIndex]["nome_favorecido"]               = $rsContrato->getCampo("nom_cgm");
            $arExportador[$inIndex]["nr_documento"]                  = $rsContrato->getCampo("cpf");
            $arExportador[$inIndex]["dt_lancamento"]                 = str_replace("/","",$_POST['dtPagamento']);
            $arExportador[$inIndex]["tipo_moeda"]                    = "BRL";
            $arExportador[$inIndex]["quant_moeda"]                   = 0;

            $nuVlrLancamento = number_format($rsContrato->getCampo("liquido"), 2, ".", "");
            $nuVlrLancamentoTotal += $nuVlrLancamento;
            $nuVlrLancamento = number_format($nuVlrLancamento, 2, "", "");

            $arExportador[$inIndex]["vlr_lancamento"]                = $nuVlrLancamento;
            $arExportador[$inIndex]["validade"]            			 = str_replace("/","",$_POST['dtPagamento']);
            $arExportador[$inIndex]["provisao"]            			 = 0;
            $arExportador[$inIndex]["finalidade"]            	     = 6;
            $arExportador[$inIndex]["reservado_banco"]     			 = "";
            $arExportador[$inIndex]["dt_real_efetivacao"]            = 0;
            $arExportador[$inIndex]["vl_real_efetivacao"]            = 0;
            $arExportador[$inIndex]["outras_informacoes"]            = $rsContrato->getCampo("registro");;
            $arExportador[$inIndex]["cnab"]                          = "";
            $arExportador[$inIndex]["aviso_favorecido"]              = 0;
            $arExportador[$inIndex]["ocorrencias"]                   = "";

            $inIndex++;
            }
            $rsContrato->proximo();
        }

        $inQuantLotes += ($rsEstagio->getNumLinhas() > 0) ? $rsEstagio->getNumLinhas() : 0;
        while (!$rsEstagio->eof()) {
            $arExportador[$inIndex]["banco"]                         = 27;
            $arExportador[$inIndex]["lote"]                          = 1;
            $arExportador[$inIndex]["registro_detalhe_lote"]         = 3;
            $arExportador[$inIndex]["numero_sequencial"]             = $inIndex+1;
            $arExportador[$inIndex]["segmento"]                      = "A";
            $arExportador[$inIndex]["tipo_movimento"]                = $_POST["inTipoMovimento"];
            $arExportador[$inIndex]["codigo_instrucao"]              = $_POST["inInstrucaoMovimento"];
            $arExportador[$inIndex]["compensacao"]  				 = 18;
            $arExportador[$inIndex]["numero_banco_favorecido"]       = $rsEstagio->getCampo("num_banco");
            $arAgencia = separarDigito($rsEstagio->getCampo("num_agencia"));
            $arExportador[$inIndex]["numero_agencia"]                = $arAgencia[0];
            $arExportador[$inIndex]["digito_agencia"]                = $arAgencia[1];
            $arConta = separarDigito($rsEstagio->getCampo("num_conta"));
            $arExportador[$inIndex]["nr_conta"]                      = $arConta[0];
            $arExportador[$inIndex]["digito_conta"]                  = $arConta[1];
            $arExportador[$inIndex]["digito_agencia_conta"]          = $arConta[1];
            $arExportador[$inIndex]["nome_favorecido"]               = $rsEstagio->getCampo("nom_cgm");
            $arExportador[$inIndex]["nr_documento"]                  = $rsEstagio->getCampo("cpf");
            $arExportador[$inIndex]["dt_lancamento"]                 = str_replace("/","",$_POST['dtPagamento']);
            $arExportador[$inIndex]["tipo_moeda"]                    = "BRL";
            $arExportador[$inIndex]["quant_moeda"]                   = 0;

            $nuVlrLancamento = number_format($rsEstagio->getCampo("liquido"), 2, ".", "");
            $nuVlrLancamentoTotal += $nuVlrLancamento;
            $nuVlrLancamento = number_format($nuVlrLancamento, 2, "", "");

            $arExportador[$inIndex]["vlr_lancamento"]                = $nuVlrLancamento;
            $arExportador[$inIndex]["validade"]            	         = str_replace("/","",$_POST['dtPagamento']);
            $arExportador[$inIndex]["provisao"]            		     = 0;
            $arExportador[$inIndex]["finalidade"]            	     = 6;
            $arExportador[$inIndex]["reservado_banco"]     		     = "";
            $arExportador[$inIndex]["dt_real_efetivacao"]            = 0;
            $arExportador[$inIndex]["vl_real_efetivacao"]            = 0;
            $arExportador[$inIndex]["outras_informacoes"]            = $rsEstagio->getCampo("numero_estagio");
            $arExportador[$inIndex]["cnab"]                          = "";
            $arExportador[$inIndex]["aviso_favorecido"]              = 0;
            $arExportador[$inIndex]["ocorrencias"]                   = "";

            $inIndex++;
            $rsEstagio->proximo();
        }

        # Pensão Judicial
        while (!$rsPensaoJudicial->eof()) {
            $arExportador[$inIndex]["banco"]                         = 27;
            $arExportador[$inIndex]["lote"]                          = 1;
            $arExportador[$inIndex]["registro_detalhe_lote"]         = 3;
            $arExportador[$inIndex]["numero_sequencial"]             = $inIndex+1;
            $arExportador[$inIndex]["segmento"]                      = "A";
            $arExportador[$inIndex]["tipo_movimento"]                = $_POST["inTipoMovimento"];
            $arExportador[$inIndex]["codigo_instrucao"]              = $_POST["inInstrucaoMovimento"];
            $arExportador[$inIndex]["compensacao"]  		 = 18;
            $arExportador[$inIndex]["numero_banco_favorecido"]       = $rsPensaoJudicial->getCampo("num_banco");
            $arAgencia = separarDigito($rsPensaoJudicial->getCampo("num_agencia"));
            $arExportador[$inIndex]["numero_agencia"]                = $arAgencia[0];
            $arExportador[$inIndex]["digito_agencia"]                = $arAgencia[1];
            $arConta = separarDigito($rsPensaoJudicial->getCampo("nr_conta"));
            $arExportador[$inIndex]["nr_conta"]                      = $arConta[0];
            $arExportador[$inIndex]["digito_conta"]                  = $arConta[1];
            $arExportador[$inIndex]["digito_agencia_conta"]          = $arConta[1];
            $arExportador[$inIndex]["nome_favorecido"]               = $rsPensaoJudicial->getCampo("nom_cgm");
            $arExportador[$inIndex]["nr_documento"]                  = $rsPensaoJudicial->getCampo("cpf");
            $arExportador[$inIndex]["dt_lancamento"]                 = str_replace("/","",$_POST['dtPagamento']);
            $arExportador[$inIndex]["tipo_moeda"]                    = "BRL";
            $arExportador[$inIndex]["quant_moeda"]                   = 0;

            $nuVlrLancamento = number_format($rsPensaoJudicial->getCampo("liquido"), 2, ".", "");
            $nuVlrLancamentoTotal += $nuVlrLancamento;
            $nuVlrLancamento = number_format($nuVlrLancamento, 2, "", "");

            $arExportador[$inIndex]["vlr_lancamento"]                = $nuVlrLancamento;
            $arExportador[$inIndex]["validade"]            		     = str_replace("/","",$_POST['dtPagamento']);
            $arExportador[$inIndex]["provisao"]            		     = 0;
            $arExportador[$inIndex]["finalidade"]            	     = 6;
            $arExportador[$inIndex]["reservado_banco"]     		     = "";
            $arExportador[$inIndex]["dt_real_efetivacao"]            = 0;
            $arExportador[$inIndex]["vl_real_efetivacao"]            = 0;
            $arExportador[$inIndex]["outras_informacoes"]            = $rsPensaoJudicial->getCampo("registro");
            $arExportador[$inIndex]["cnab"]                          = "";
            $arExportador[$inIndex]["aviso_favorecido"]              = 0;
            $arExportador[$inIndex]["ocorrencias"]                   = "";

            $inIndex++;
            $inNumSequencial++;
            $rsPensaoJudicial->proximo();
        }

        #####################################CABEÇALHO ARQUIVO####################################
        $arCabecalhoArquivo = array();
        $arCabecalhoArquivo[0]['banco']                                        = 27;
        $arCabecalhoArquivo[0]['lote']                                         = 0;
        $arCabecalhoArquivo[0]['registro']                                     = 0;
        $arCabecalhoArquivo[0]['cnab']                                         = "";
        $arCabecalhoArquivo[0]['tipo_inscricao']                               = 2;
        $arCabecalhoArquivo[0]['numero_inscricao']                             = SistemaLegado::pegaConfiguracao("cnpj",2,Sessao::getExercicio());
        $arCabecalhoArquivo[0]['convenio']                                     = $rsConfiguracaoConvenio->getCampo("cod_convenio_banco");
        $arCabecalhoArquivo[0]['codigo_agencia']                               = $inAgenciaConvenio;
        $arCabecalhoArquivo[0]['digito_verificador_agencia']                   = $inDigitoVerificadorAgencia;
        $arCabecalhoArquivo[0]['numero_conta_corrente']                        = $inContaConvenio;
        $arCabecalhoArquivo[0]['digito_verificador_conta_corrente']            = $inDigitoVerificadorConta;
        $arCabecalhoArquivo[0]['digito_verificador_agencia_conta_corrente']    = $inDigitoVerificadorConta;
        $arCabecalhoArquivo[0]['nome_empresa']                                 = SistemaLegado::pegaConfiguracao("nom_prefeitura",2,Sessao::getExercicio());
        $arCabecalhoArquivo[0]['nome_banco']                                   = "BANCO EST STA CATARINA SA";
        $arCabecalhoArquivo[0]['codigo_remessa']                               = 1;
        $arCabecalhoArquivo[0]['data_geracao']                                 = str_replace("/","",$_POST["dtGeracaoArquivo"]);
        $arCabecalhoArquivo[0]['hora_geracao']                                 = date("His");
        $arCabecalhoArquivo[0]['numero_sequencial']                            = $inNumeroSequencial;
        $arCabecalhoArquivo[0]['numero_versao_layout']                         = 20;
        $arCabecalhoArquivo[0]['densidade_gravacao_arquivo']                   = 0;
        $arCabecalhoArquivo[0]['sistema']                   				   = "AGL";
        $arCabecalhoArquivo[0]['movimento']                   				   = $_POST["inTipoMovimento"];;
        $arCabecalhoArquivo[0]['reservado_banco']                              = "";
        $arCabecalhoArquivo[0]['reservado_empresa']                            = "";

        $rsCabecalhoArquivo = new RecordSet();
        $rsCabecalhoArquivo->preenche($arCabecalhoArquivo);
        $obExportador->roUltimoArquivo->addBloco($rsCabecalhoArquivo);
        $inNumeroSequencial++;

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("banco");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("lote");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnab");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(9);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_inscricao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_inscricao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("convenio");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codigo_agencia");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(05);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("digito_verificador_agencia");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_conta_corrente");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("digito_verificador_conta_corrente");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("digito_verificador_agencia_conta_corrente");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_empresa");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(30);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_banco");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(30);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnab");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codigo_remessa");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_geracao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("hora_geracao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_versao_layout");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("densidade_gravacao_arquivo");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sistema");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("movimento");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_banco");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_empresa");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnab");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(29);

        #######################################    CABEÇALHO DO LOTE    ######################################

        include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoMunicipio.class.php");
        $obTAdministracaoMunicipio = new TMunicipio();
        $obTAdministracaoMunicipio->setDado("cod_municipio",SistemaLegado::pegaConfiguracao("cod_municipio",2,Sessao::getExercicio()));
        $obTAdministracaoMunicipio->setDado("cod_uf",SistemaLegado::pegaConfiguracao("cod_uf",2,Sessao::getExercicio()));
        $obTAdministracaoMunicipio->recuperaPorChave($rsMunicipio);

        include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoUF.class.php");
        $obTAdministracaoUF = new TUF();
        $obTAdministracaoUF->setDado("cod_uf",SistemaLegado::pegaConfiguracao("cod_uf",2,Sessao::getExercicio()));
        $obTAdministracaoUF->recuperaPorChave($rsUF);

        $stCEP  = SistemaLegado::pegaConfiguracao("cep",2,Sessao::getExercicio());
        $inCEP1 = substr($stCEP,0,5);
        $inCEP2 = substr($stCEP,4,3);

        $arCabecalhoLote[0]['banco']                                        = 27;
        $arCabecalhoLote[0]['lote']                                         = 1;
        $arCabecalhoLote[0]['registro']                                     = 1;
        $arCabecalhoLote[0]['operacao']                                     = "C";
        $arCabecalhoLote[0]['servico']                                      = 30;
        $arCabecalhoLote[0]['forma_lancamento']                             = 1;
        $arCabecalhoLote[0]['layout_lote']                                  = 20;
        $arCabecalhoLote[0]['cnab']                                         = "";
        $arCabecalhoLote[0]['tipo_inscricao']                               = 2;
        $arCabecalhoLote[0]['numero_inscricao']                             = SistemaLegado::pegaConfiguracao("cnpj",2,Sessao::getExercicio());
        $arCabecalhoLote[0]['convenio']                                     = $rsConfiguracaoConvenio->getCampo("cod_convenio_banco");
        $arCabecalhoLote[0]['codigo_agencia']                               = $inAgenciaConvenio;
        $arCabecalhoLote[0]['digito_verificador_agencia']                   = $inDigitoVerificadorAgencia;
        $arCabecalhoLote[0]['numero_conta_corrente']                        = $inContaConvenio;
        $arCabecalhoLote[0]['digito_verificador_conta_corrente']            = $inDigitoVerificadorConta;
        $arCabecalhoLote[0]['digito_verificador_agencia_conta_corrente']    = $inDigitoVerificadorConta;
        $arCabecalhoLote[0]['nome_empresa']                                 = SistemaLegado::pegaConfiguracao("nom_prefeitura",2,Sessao::getExercicio());
        $arCabecalhoLote[0]['mensagem']                                     = "";
        $arCabecalhoLote[0]['logradouro']                                   = SistemaLegado::pegaConfiguracao("logradouro",2,Sessao::getExercicio());
        $arCabecalhoLote[0]['numero_local']                                 = SistemaLegado::pegaConfiguracao("numero",2,Sessao::getExercicio());
        $arCabecalhoLote[0]['complemento']                                  = SistemaLegado::pegaConfiguracao("complemento",2,Sessao::getExercicio());
        $arCabecalhoLote[0]['cidade']                                       = $rsMunicipio->getCampo("nom_municipio");
        $arCabecalhoLote[0]['cep']                                          = $inCEP1;
        $arCabecalhoLote[0]['complemento_cep']                              = $inCEP2;
        $arCabecalhoLote[0]['estado']                                       = $rsUF->getCampo("sigla_uf");
        $arCabecalhoLote[0]['cnab']                                         = "";
        $arCabecalhoLote[0]['ocorrencias']                                  = "";

        $rsCabecalhoLote = new RecordSet;
        $rsCabecalhoLote->preenche($arCabecalhoLote);

        $obExportador->roUltimoArquivo->addBloco($rsCabecalhoLote);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("banco");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("lote");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("operacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("servico");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("forma_lancamento");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("layout_lote");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnab");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_inscricao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_inscricao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("convenio");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codigo_agencia");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(05);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("digito_verificador_agencia");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_conta_corrente");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("digito_verificador_conta_corrente");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("digito_verificador_agencia_conta_corrente");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_empresa");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(30);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("mensagem");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(40);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("logradouro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(30);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_local");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("complemento");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cidade");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cep");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("complemento_cep");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("estado");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnab");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ocorrencias");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);

        ##################################### DETALHE DO LOTE ##############################################

        $rsExportador = new RecordSet();
        $rsExportador->preenche($arExportador);

        $inQuantRegistrosLote = ($rsExportador->getNumLinhas() == -1) ? 0 : $rsExportador->getNumLinhas();
        $arArquivoDadosExtras[$stNomeArquivo]['qtd'] = $inQuantRegistrosLote;
        $nuLiquidoTotal = number_format($nuVlrLancamentoTotal,2,",",".");
        $arArquivoDadosExtras[$stNomeArquivo]['total'] = $nuLiquidoTotal;

        $arQuantMoedas = $rsExportador->getSomaCampo("quant_moeda");
        $nuQuantMoedas = $arQuantMoedas["quant_moeda"];

        $obExportador->roUltimoArquivo->addBloco($rsExportador);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("banco");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("lote");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("registro_detalhe_lote");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("segmento");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_movimento");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codigo_instrucao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("compensacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_banco_favorecido");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_agencia");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("digito_agencia");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nr_conta");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("digito_conta");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("digito_agencia_conta");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_favorecido");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(30);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nr_documento");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_lancamento");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_moeda");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("quant_moeda");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vlr_lancamento");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("validade");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("provisao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("finalidade");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_banco");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(9);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_real_efetivacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_real_efetivacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("outras_informacoes");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(40);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnab");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("aviso_favorecido");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ocorrencias");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);

        ##################################### RODAPÉ DO LOTE ################################################
        $arRodapeLote[0]['banco']                   = 27;
        $arRodapeLote[0]['lote']                    = 1;
        $arRodapeLote[0]['registro']                = 5;
        $arRodapeLote[0]['cnab']                    = "";
        $arRodapeLote[0]["quant_registros"]         = ( $obExportador->roUltimoArquivo->arBlocos[ count($obExportador->roUltimoArquivo->arBlocos)-1 ]->rsRecordSet->getNumLinhas() > 0 ) ? $obExportador->roUltimoArquivo->arBlocos[ count($obExportador->roUltimoArquivo->arBlocos)-1 ]->rsRecordSet->getNumLinhas() : 0;

        $nuVlrLancamentoTotal = number_format($nuVlrLancamentoTotal, 2, ".", "");
        $nuVlrLancamentoTotal = number_format($nuVlrLancamentoTotal, 2, "", "");

        $arRodapeLote[0]["valor_debito_credito"]    = $nuVlrLancamentoTotal;
        $arRodapeLote[0]["quant_moedas"]            = $nuQuantMoedas;
        $arRodapeLote[0]["ocorrencias"]             = "";

        $rsRodapeLote = new RecordSet;
        $rsRodapeLote->preenche($arRodapeLote);

        $obExportador->roUltimoArquivo->addBloco($rsRodapeLote);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("banco");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("lote");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnab");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(9);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("quant_registros");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_debito_credito");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(18);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("quant_moedas");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(18);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnab");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(171);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ocorrencias");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);

        ##########################################RODAPÉ ARQUIVO#########################################
        $arRodape[0]['banco']           = 27;
        $arRodape[0]['lote']            = 9999;
        $arRodape[0]['registro']        = 9;
        $arRodape[0]['cnab']            = "";
        $arRodape[0]["quant_registros"] = $inQuantLotes+4;
        $arRodape[0]["quant_lotes"]     = 1;
        $arRodape[0]["quant_contas"]    = 0;
        $rsRodape = new RecordSet;
        $rsRodape->preenche($arRodape);

        $obExportador->roUltimoArquivo->addBloco($rsRodape);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("banco");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("lote");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnab");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(9);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("quant_lotes");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("quant_registros");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("quant_contas");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnab");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(205);

        ########################UPDATE CONFIGURACAO########################################################

        include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php");
        $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao();
        $obTAdministracaoConfiguracao->setDado("exercicio",Sessao::getExercicio());
        $obTAdministracaoConfiguracao->setDado("cod_modulo",40);

        $arPeriodoMovimentacaoAtual = explode("/",$rsUltimaMovimentacao->getCampo('dt_final'));
        $dtPeriodoMovimentacaoAtual = $arPeriodoMovimentacaoAtual[2]."-".$arPeriodoMovimentacaoAtual[1]."-".$arPeriodoMovimentacaoAtual[0];

        $obTAdministracaoConfiguracao->setDado("parametro","dt_num_sequencial_arquivo_besc".Sessao::getEntidade());
        $obTAdministracaoConfiguracao->setDado("valor",$dtPeriodoMovimentacaoAtual);
        $obTAdministracaoConfiguracao->alteracao();

        $obTAdministracaoConfiguracao->setDado("parametro","num_sequencial_arquivo_besc".Sessao::getEntidade());
        $obTAdministracaoConfiguracao->setDado("valor",$inNumeroSequencial);
        $obTAdministracaoConfiguracao->alteracao();

        $rsContasConvenio->proximo();
}
$obExportador->Show();
Sessao::write("arArquivoDadosExtras",$arArquivoDadosExtras);
Sessao::encerraExcecao();

function separarDigito($stString)
{
    $inNumero = preg_replace( "/[^0-9a-zA-Z]/i","",$stString);
    $inDigito = $inNumero[strlen($inNumero)-1];
    $inNumero = substr($inNumero,0,strlen($inNumero)-1);

    return array($inNumero,$inDigito);
}
?>
