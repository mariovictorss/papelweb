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
#!/usr/bin/env php
<?php
echo "\n";

function _echo($msg)
{
    echo ">>  ".$msg . " \n";
}

# Uso: gerar_deb diretorio_saida

if ($_SERVER["argc"] != 3) {
    echo "Uso deve ser:\n";
    echo "./gerar_deb.php dir_pacote_gestao diretorio_saida\n";
    exit("Saindo\n");
}

# Local de desova

$param_local = escapeshellcmd(escapeshellcmd($_SERVER['argv'][1]));
$param_saida = escapeshellcmd(escapeshellcmd($_SERVER['argv'][2]));

if ( !substr("abcdef", -1) == '/') {}
$param_local = (substr($param_local, - 1) == '/') ? $param_local : $param_local."/";

#arrumar caminho urbem
if (!file_exists($param_local.'index.php')) {
    _echo('Diretório informado não é um pacote Urbem valido');
    exit("Saindo\n");
}

# validar
if (!file_exists($param_saida)) {
    _echo( sprintf('Local de Saida %s não existe',$param_saida));
    exit("Saindo\n");
}

# Determinar versao
$ar_local = explode(DIRECTORY_SEPARATOR,$param_local);

list($gestao,$versao) = explode('_',$ar_local[count($ar_local) - 2]);

_echo('Gestão: ' . $gestao);
_echo('Versão: ' . $versao);

# Diretorio Temp
$pkg_tmp_dir = "/tmp/" . md5(round(time()));

# Criar Diretorio temp
_echo('Criando Diretório Temporario');
if (!@mkdir($pkg_tmp_dir,0700)) {
    _echo('Erro ao criar Diretorio temporario');
    exit("Saindo\n");
}

/**
 * Criar estrutura
 */

_echo('Criando estrutura do pacote debian');
if (!@mkdir($pkg_tmp_dir . "/DEBIAN",0755)) {
    _echo('Erro criando estrutura do pacote debian');
    exit("Saindo\n");
}
if (!@mkdir($pkg_tmp_dir . "/tmp",0755)) {
    _echo('Erro criando estrutura do pacote debian');
    exit("Saindo\n");
}
# Criar arvquivo control
$control_file = $pkg_tmp_dir . "/DEBIAN/control";
$fpControl = fopen($control_file,'w+');
$arOpts = array('pkg' => $gestao,
                'versao' => $versao,
                'nome_gestao' => $gestao
                );
$stControlFile = get_control_content($arOpts);

$ret = fwrite($fpControl,$stControlFile);
fclose($fpControl);

# Criar arquivo postinst
$postinst_file = $pkg_tmp_dir . "/DEBIAN/postinst";
$fpPostInst = fopen($postinst_file,'w+');

$stPostInstFile = <<<POSTINSTFILE
#!/bin/bash
/tmp/atualizador.sh
POSTINSTFILE;

$ret = fwrite($fpPostInst,$stPostInstFile);
fclose($fpPostInst);
# Permissao de Execucao para o PostInst
chmod($postinst_file ,0755);

# Criar arquivo postrm
$postrm_file = $pkg_tmp_dir . "/DEBIAN/postrm";
$fpPostRm = fopen($postrm_file,'w+');

$stPostRmFile = <<<POSTRMFILE
#!/bin/bash
echo "Ok!"
POSTRMFILE;

$ret = fwrite($fpPostRm,$stPostRmFile);
fclose($fpPostRm);
# Permissao de Execucao para o PostRm
chmod($postrm_file ,0755);

/**
 * Copiar Fontes
 */
_echo('Copiando arquivos');
$comando = sprintf("cp -r %s %s", $param_local."*" , $pkg_tmp_dir."/tmp/");
system($comando,$retorno);

# Permissao de Execucao para o atualizador.sh
chmod( $pkg_tmp_dir."/tmp/atualizador.sh",0755);

/**
 * Removendo arquivos não inclusos em pacote ('')
 */
_echo('Removendo arquivos não inclusos em pacote(\'Limpando arquivos de dev(styler, empacotador, etc)\') ');

# Gerando Arquivo .deb
_echo('Gerando Arquivo .deb');
$comando = sprintf("dpkg-deb -b %s %s", $pkg_tmp_dir , $param_saida);
system($comando,$retorno);

/**
 * Limpar
 */
_echo('Limpando');

if (!@remove_dir($pkg_tmp_dir)) {
    _echo('Falha ao limpar diretorios');
}

echo "\n";

/* Funcao para deletar diretorio recursivamente */
function remove_dir($dir)
{
    if (is_dir($dir)) {
        $dir = (substr($dir, -1) != "/")? $dir."/":$dir;
        $openDir = opendir($dir);
        while ($file = readdir($openDir)) {
            if (!in_array($file, array(".", ".."))) {
                if(!is_dir($dir.$file))
                    @unlink($dir.$file);
                else
                    remove_dir($dir.$file);
            }
        }
        closedir($openDir);

        return @rmdir($dir);
    }
}

# gera conteudo para arquivo control do debian
function get_control_content($opts)
{
    $stConteudo = <<<CONTEUDO
Package: %s
Priority: optional
Version: %s
Architecture: all
Maintainer: Fausto Vaz Ribeiro (fausto.ribeiro@cnm.org.br)
Description: Pacote URBEM %s %s

CONTEUDO;

    return sprintf($stConteudo,$opts['pkg'],$opts['versao'],$opts['nome_gestao'],$opts['versao']);
}
