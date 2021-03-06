<?
/**
    * Página de Relatório Despesas SIOPS
    * Data de Criação  : 12/06/2008


    * @author Rodrigo Soares Rodrigues
    
    * Casos de uso : uc-02.01.01
    
    * $Id: OCGeraDespesasSIOPS.php 54721 2013-04-30 18:19:19Z eduardoschitz $

*/

include_once ( "../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php" );
include_once ( "../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php"       );
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php"                                    );


$preview = new PreviewBirt(2,8,1);
$preview->setReturnURL(CAM_GF_ORC_INSTANCIAS.'relatorio/FLDespesasSIOPS.php');
$preview->setTitulo('Relatório do Birt');
$preview->setVersaoBirt( '2.5.0' );

$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado( 'exercicio'   , Sessao::getExercicio() );
$obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, "and e.cod_entidade in (".implode(',',$_REQUEST['inCodEntidade']).")" );

$preview->addParametro ( 'cod_entidade', implode(',', $_REQUEST['inCodEntidade'] ) );

if( count($_REQUEST['inCodEntidade']) > 0 ){
    $preview->addParametro( 'nom_entidade', $rsEntidade->getCampo('nom_cgm') );
}else{
    $preview->addParametro( 'nom_entidade', '' );
}   
$preview->addParametro( 'orgao', implode(',', $_REQUEST['inCodOrgao']) );

$preview->addParametro( 'tipo_conta', $_REQUEST['stEstiloConta'] );

if($_REQUEST['cbmTrimestre']){
    $preview->addParametro( 'periodo', $_REQUEST['cmbTrimestre']."º Trimestre"  );
}

if($_REQUEST['cmbSemestre']){
    $preview->addParametro( 'periodo', $_REQUEST['cmbSemestre']."º Semestre"  );
}

if($_REQUEST['cmbBimestre']){
    $preview->addParametro( 'periodo', $_REQUEST['cmbBimestre']."º Bimestre"  );
}


$stDataInicial = '01/01/'.Sessao::getExercicio();
$preview->addParametro ( 'data_inicial', $stDataInicial );

    if($_REQUEST['cmbTrimestre']){
        switch ( $_REQUEST['cmbTrimestre'] ) {
            case '1':
                $preview->addParametro( 'data_fim', '31/03/'.Sessao::getExercicio() );
            break;
            case '2':
                $preview->addParametro( 'data_fim', '30/06/'.Sessao::getExercicio() );
            break;
            case '3':
                $preview->addParametro( 'data_fim', '30/09/'.Sessao::getExercicio() );
            break;
            case '4':
                $preview->addParametro( 'data_fim', '31/12/'.Sessao::getExercicio() );
            break;
        }
    }

    if($_REQUEST['cmbSemestre']){
        switch ( $_REQUEST['cmbSemestre'] ) {
            case '1':
                $preview->addParametro( 'data_fim', '30/06/'.Sessao::getExercicio() );
            break;
            case '2':
                $preview->addParametro( 'data_fim', '31/12/'.Sessao::getExercicio() );
            break;
        }
    }
    if($_REQUEST['cmbBimestre']){
        switch ( $_REQUEST['cmbBimestre'] ) {
            case '1':
                //metodo criado para manipular datas, trazendo o dia anterior ao dia 1º de março
                $preview->addParametro( 'data_fim', SistemaLegado::somaOuSubtraiData('01/03/'.Sessao::getExercicio(),false,1,'day') );
            break;
            case '2':
                $preview->addParametro( 'data_fim', '30/04/'.Sessao::getExercicio() );
            break;
            case '3':
                $preview->addParametro( 'data_fim', '30/06/'.Sessao::getExercicio() );
            break;
            case '4':
                $preview->addParametro( 'data_fim', '31/08/'.Sessao::getExercicio() );
            break;
            case '5':
                $preview->addParametro( 'data_fim', '31/10/'.Sessao::getExercicio() );
            break;
            case '6':
                $preview->addParametro( 'data_fim', '31/12/'.Sessao::getExercicio() );
            break;
    
        }
    }


$preview->addAssinaturas(Sessao::read('assinaturas'));
$preview->preview();
?>
