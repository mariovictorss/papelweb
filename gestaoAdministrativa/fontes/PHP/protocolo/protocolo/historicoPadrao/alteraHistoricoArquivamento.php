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
* Arquivo de implementação de manutenção de histórico de arquivamento
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 24719 $
$Name$
$Author: domluc $
$Date: 2007-08-13 17:53:04 -0300 (Seg, 13 Ago 2007) $

Casos de uso: uc-01.06.92
*/

include '../../../framework/include/cabecalho.inc.php';
include (CAM_FRAMEWORK."legado/funcoesLegado.lib.php"    );
include (CAM_FRAMEWORK."legado/paginacaoLegada.class.php");
include '../configProtocolo.class.php';
include (CAM_FRAMEWORK."legado/auditoriaLegada.class.php");
setAjuda('uc-01.06.92');

if (!(isset($_REQUEST["ctrl"]))) {
    $ctrl = 0;
} else {
    $ctrl = $_REQUEST["ctrl"];
}

if (!isset($_REQUEST["pagina"])) {
    $pagina = 0;
} else {
    $pagina = $_REQUEST["pagina"];
}

if (isset($pagina)) {
    Sessao::write('pagina',$pagina);
}
?>

 <script type="text/javascript">
 function zebra(id, classe)
 {
       var tabela = document.getElementById(id);
        var linhas = tabela.getElementsByTagName("tr");
            for (var i = 0; i < linhas.length; i++) {
            ((i%2) != 0) ? linhas[i].className = classe : void(0);
        }
    }
</script>

<?php
switch ($ctrl) {
case 0:
    if (isset($_REQUEST["acao"])) {
           Sessao::write('pagina','');
           $sql =  "select cod_historico, nom_historico from sw_historico_arquivamento";
           Sessao::write('sSQLs',$sql);
    }

        $paginacao = new paginacaoLegada;
        $paginacao->pegaDados(Sessao::read('sSQLs'),"10");
        $paginacao->pegaPagina($pagina);
        $paginacao->geraLinks();
        $paginacao->pegaOrder("nom_historico","ASC");
        $sSQL = $paginacao->geraSQL();
        //print $sSQL;
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $exec .= "
 <table width=100% id='processos'>
    <tr>
        <td colspan=4 class=alt_dados>Registros de Motivo do Arquivamento</td>
    </tr>
        ";
    $cont = 1;
    $exec .="
        <tr>
        <td class=labelleftcabecalho width=5%>&nbsp;</td>
        <td class=labelleftcabecalho width=12%>Código</td>
        <td class=labelleftcabecalho width=80%>Descrição</td>
        <td class=labelleftcabecalho>&nbsp;</td>
        </tr>";
        while (!$dbEmp->eof()) {
                $codHistorico = $dbEmp->pegaCampo("cod_historico");
                $nomHistorico = $dbEmp->pegaCampo("nom_historico");
                $dbEmp->vaiProximo();
                $exec .= "
            <tr>
             <td class=show_dados_center_bold width=5%>".$cont."</td>
             <td class=show_dados width=5%>".$codHistorico."</td>
             <td class=show_dados>".$nomHistorico."</td>
             <td class='botao'>
             <a href='".$PHP_SELF."?".Sessao::getId()."&codHistorico=".$codHistorico."&ctrl=1&pagina=".$pagina."'>
             <img src='".CAM_FW_IMAGENS."btneditar.gif' border=0></a></td>
            </tr>";
        $cont++;
        }
        $exec .= "</table>";
        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
        echo $exec;
        echo "<table id= 'paginacao' width=695 align=center><tr><td align=center><font size=2>";
        $paginacao->mostraLinks();
        echo "</font></tr></td></table>";
?>
        <script>zebra('processos','zb');</script>
<?php
break;
case 1:

$configura = new configProtocolo;
$configura->mostraHistoricoArquivamento($_REQUEST["codHistorico"]);
?>
<script type="text/javascript">

      function Valida()
      {
        var mensagem = "";
        var erro = false;
        var campo;
        var campoaux;

        campo = trim( document.frm.nomHistorico.value );
            if (campo == "") {
            mensagem += "@O campo Descrição é obrigatório";
            erro = true;
         }

            if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
            return !(erro);
      }
      function Salvar()
      {
         var f = document.frm;
         f.ok.disabled = true;
         if (Valida()) {
            document.frm.submit();
         } else {
            f.ok.disabled = false;
         }
      }
     function Cancela()
     {
           pag = "<?=$PHP_SELF?>?<?=Sessao::getId()?>&pagina=<?=$pagina?>&ctrl=0";
           mudaTelaPrincipal(pag);
     }
</script>

<form name="frm" action="alteraHistoricoArquivamento.php?<?=Sessao::getId();?>&ctrl=2" method="POST" onSubmit='return Valida();'>
<table width=100%>
<tr>
    <td class="alt_dados" colspan=2>Motivo do Arquivamento</td>
</tr>
<input type="hidden" name="pagina" value="<?=$pagina;?>">
<tr>
    <td class=label width=30% >Código</td>
    <td class=field><?=$configura->codigo;?></td>
</tr>
<tr>
    <td class=label title="Descrição do Motivo do Arquivamento">*Descrição</td>
    <td class=field><input type="text" name="nomHistorico" value="<?=$configura->nome;?>" size=60 maxlength=60>
        <input type="hidden" name="codHistorico" value="<?=$configura->codigo;?>">
    </td>
</tr>

<tr>
    <td class=field colspan="2">
        <?=geraBotaoAltera();?>
    </td>
</tr>

</table>
</form>
<?php
    break;
case 2:
    $ok = true;
    if (!comparaValor("nom_historico", $_REQUEST["nomHistorico"], "sw_historico_arquivamento","and cod_historico <> '".$_REQUEST["codHistorico"]."'",1)) {
        alertaAviso($PHP_SELF,"O Motivo do Arquivamento ".$_REQUEST["nomHistorico"]." já existe!","unica","erro","'.Sessao::getId().'");
        $ok = false;
    }

    if ($ok) {

    $configura = new configProtocolo;
    $configura->setaVariaveis($_REQUEST["codHistorico"], $_REQUEST["nomHistorico"]);

    if ($configura->alteraHistoricoArquivamento()) {
        $audicao = new auditoriaLegada;
        $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $_REQUEST["nomHistorico"]);
        $audicao->insereAuditoria();
        echo '
        <script type="text/javascript">
        alertaAviso("'.$_REQUEST["nomHistorico"].'","alterar","aviso", "'.Sessao::getId().'&pagina='.$pagina.'");
        mudaTelaPrincipal("'.$PHP_SELF.'?'.Sessao::getId().'&pagina='.$pagina.'");
        </script>';
    } else {
        echo '
        <script type="text/javascript">
            alertaAviso("'.$_REQUEST["nomHistorico"].'","n_alterar","erro", "'.Sessao::getId().'");
            mudaTelaPrincipal("'.$PHP_SELF.'?'.Sessao::getId().'");
        </script>';
    }
    }
    break;
}
include '../../../framework/include/rodape.inc.php';
//include "../../includes/rodape.php";
?>
