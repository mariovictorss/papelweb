#!/bin/bash
#/*
#    **********************************************************************************
#    *                                                                                *
#    * @package URBEM CNM - Solu��es em Gest�o P�blica                                *
#    * @copyright (c) 2013 Confedera��o Nacional de Munic�pos (urbem@cnm.org.br)      *
#    * @author Confedera��o Nacional de Munic�pios                                    *
#    *                                                                                *
#    * Este programa � software livre; voc� pode redistribu�-lo e/ou modific�-lo  sob *
#    * os termos da Licen�a P�blica Geral GNU conforme publicada pela  Free  Software *
#    * Foundation; tanto a vers�o 2 da Licen�a, como (a seu crit�rio) qualquer vers�o *
#    * posterior.                                                                     *
#    *                                                                                *
#    * Este  programa  �  distribu�do  na  expectativa  de  que  seja  �til,   por�m, *
#    * SEM NENHUMA GARANTIA; nem mesmo a garantia impl�cita  de  COMERCIABILIDADE  OU *
#    * ADEQUA��O A UMA FINALIDADE ESPEC�FICA. Consulte a Licen�a P�blica Geral do GNU *
#    * para mais detalhes.                                                            *
#    *                                                                                *
#    * Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral  do  GNU  junto  com *
#    * este programa; se n�o, escreva para  a  Free  Software  Foundation,  Inc.,  no *
#    * endere�o 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.               *
#    *                                                                                *
#    **********************************************************************************
#*/

##############################################
# Script para instalação do ambiente Urbem   #
# 0- instalar java                           #
# 1- instalar apache                         #
# 2- instalar php                            #
# 3- instalar postgres                       #
# 4- instalar zend optimizer                 #
# 5- instalar tomcat                         #
# 6- configura base padrão urbem             #
##############################################


HOST="ftp.sw.cnm.org.br"
USER="convidado"
PASS="5p1D34m@N"

#Se não tiver o diretório, cria
if [ ! -d URBEM-INSTALL ]; then
    mkdir URBEM-INSTALL
fi

cd URBEM-INSTALL
SW_ROOT=`pwd`

controle() {
    #executa o comando e joga o resultado para o arquivo de log
    eval $1

    retornoComando=$?
    comando=$1
    ordem=$2

    #se ocorrer algum erro, vai para o método erro e informa qual o comando que deu problema
    if [ $retornoComando -gt 0 ]; then
        erro "$comando" $ordem
    fi

}

downloads() {
    cd $SW_ROOT
    echo "#################### Baixando pacotes necessários para instalação... ####################"

    # Pacotes baixados por FTP
    pacotesFTP[0]='ZendOptimizer.tar.bz2'
    pacotesFTP[1]='php-5.2.17.tar.bz2'
    pacotesFTP[2]='php.ini'
    pacotesFTP[3]='postgresql-8.1.23.tar.bz2'
    pacotesFTP[4]='tomcat-urbem.tar.bz2'
    pacotesFTP[5]='postgres'
    pacotesFTP[6]='postgresql.conf'
    pacotesFTP[7]='urbem.sql'
    pacotesFTP[8]='config.yml'

    for pacoteFTP in "${pacotesFTP[@]}"
    do
        # Verifica se o arquivo já existe
        if [ ! -e $pacoteFTP ]; then
            ftp -invp $HOST<<EOF 
            user $USER $PASS
            ha 
            bi 
            get	$pacoteFTP
            bye 
EOF
    
            if [ $? -gt 0 ]; then
                echo "#################### Ocorreu um erro ao baixar o pacote $pacote por FTP ####################"
                exit
            fi
        fi
    done

    # Pacotes instalados por apt-get

    #pacotes referentes ao sistema
    pacotes[0]='build-essential'
    pacotes[1]='bzip2'
    pacotes[2]='zip'
    pacotes[3]='unzip'
    pacotes[4]='tar'
    pacotes[5]='dialog'

    # Instalação de libs do postgres
    pacotes[6]='libreadline-dev'
    pacotes[7]='zlib1g-dev'

    # Instalação do Apache2 e libs necessárias
    pacotes[8]='apache2'
    pacotes[9]='apache2-threaded-dev'
    pacotes[10]='libxml2-dev'

    for pacote in "${pacotes[@]}"
    do
        if [ $pacote == 'bzip2' ]; then
            apt-get install $pacote -y --reinstall
        else
            apt-get install $pacote -y
        fi 

        if [ $? -gt 0 ]; then
            echo "#################### Ocorreu um erro ao baixar o pacote $pacote ####################"
            exit
        fi
    done
}


erro() {
    echo -e "#################### Ocorreu um erro ao tentar instalar o $metodo. Erro ao executar o seguinte comando: $1 #################### \n"

    #Verifica quantos comandos devem ser desfeitos devido ao erro
    if [ -n "$2" ]; then
        desfazer $2
    fi

    exit
}


desfazer() {
    if [ $metodo == 'postgres' ]; then
        #Se tiver que desfazer mais de 4 comandos, então dá um stop no banco para poder remover o usuário postgres
        if [ $1 -gt 1 ]; then
            /etc/init.d/postgres stop
        fi

        echo "#################### Desfazendo instalação do postgres.. ####################"
        for (( i=1; i <= $1; i++ ))
        do
            if [ $i -eq 1 ]; then
                #remove o diretório descompactado do tar
                cd $SW_ROOT
                rm postgresql-8.1.23/ -rf 

                #guarda o nome dos links que devem ser removidos de /usr/bin/ antes de remover o diretório pgsql criado
                files=$(ls /usr/local/pgsql/bin)
                rm /usr/local/pgsql -rf
            elif [ $i -eq 2 ]; then
                #remove o usuário postgres
                userdel postgres --force
            elif [ $i -eq 3 ]; then
                #remove os links para o diretório pgsql removido anteriormente
                for file in $files
                do
                    rm /usr/bin/$file
                done
            elif [ $i -eq 6 ]; then
                #remove o postgres da inicialização
                rm /etc/init.d/postgres
            fi
        done
    fi

    if [ $metodo == 'php' ]; then
        echo "#################### Desfazendo instalação do PHP.. ####################"
        for (( i=1; i <= $1; i++ ))
        do
            if [ $i -eq 1 ]; then
                #remove o diretório criado para o php
                rm /etc/php5 -rf
            elif [ $i -eq 2 ]; then
                #remove o diretório descompactado do tar
                cd $SW_ROOT
                rm php-5.2.17 -rf
            elif [ $i -eq 3 ]; then
                #remove o diretório descompactado do tar
                cd $SW_ROOT
                rm ZendOptimizer -rf
            elif [ $i -eq 4 ]; then
                #remove o diretório criado para o Zend
                rm /usr/local/Zend -rf
            fi
        done
    fi

    if [ $metodo == 'tomcat' ]; then
        echo "#################### Desfazendo instalação do tomcat.. ####################"
        for (( i=1; i <= $1; i++ ))
        do
            if [ $i -eq 1 ]; then
                #remove o diretório descompactado do tar
                cd $SW_ROOT
                rm tomcat-urbem -rf
            elif [ $i -eq 2 ]; then
                #remove o diretório criado para o tomcat
                rm /usr/local/tomcat-urbem -rf
            fi
        done
    fi

    if [ $metodo == 'configuracao' ]; then
        echo "#################### Desfazendo configurações ####################"
        for (( i=1; i <= $1; i++ ))
        do
            if [ $i -eq 1 ]; then
                #remove o diretório criado para o urbem
                rm /var/www/urbem -rf
            fi
        done
    fi
}

postgres(){
    metodo="postgres"
    if [ ! -d /usr/local/pgsql ]; then
        echo "#################### Instalação do Postgres: ####################"
        cd $SW_ROOT
        echo "#################### Descompactando tar... ####################"
        controle "tar -jxvf postgresql-8.1.23.tar.bz2" 1

        echo "#################### Configurando compilação.. ####################"
        #compilação do postgres
        cd postgresql-8.1.23
        controle "./configure" 1
        echo "#################### Rodando make.. ####################"
        controle "make" 1
        echo "#################### Rodando make install.. ####################"
        controle "make install" 1

        echo "#################### Informe a senha do usuário postgres do linux ####################"
        controle "adduser postgres --home /usr/local/pgsql --no-create-home --shell /bin/sh --gecos '' -q" 2
        echo "#################### Criando diretório data.. ####################"
        controle "mkdir /usr/local/pgsql/data" 2
        controle "chown postgres /usr/local/pgsql/data" 2

        #inicialização do postgres
        echo "#################### Inicializando postgres.. ####################"
        controle "su postgres -c \"/usr/local/pgsql/bin/initdb -D /usr/local/pgsql/data --locale='pt_BR' --encoding='UTF-8'\"" 3

        #pega o arquivo de configuração padrão criado pela CNM
        cd /usr/local/pgsql/data
        controle "su postgres -c \"mv postgresql.conf postgresql.conf.default \"" 2
        controle "su postgres -c \"cp $SW_ROOT/postgresql.conf . \"" 2

        echo "#################### Criando links para /usr/bin/ ####################"
        #colocando os links simbolicos no /usr/bin
        cd /usr/local/pgsql/bin
        for file in `ls`
        do
            if [ ! -e /usr/bin/$file ]; then
                ln -s /usr/local/pgsql/bin/$file /usr/bin/$file
            fi
        done

        #adiciona postgres no diretório de inicialização do sistema
        cd /etc/init.d/
        controle "cp $SW_ROOT/postgres ." 4
        controle "chmod +x postgres" 4

        echo "#################### Iniciando banco para alteração da senha.. ####################"
        #inicia o banco
        controle "./postgres start" 4

        sleep 7

        echo "#################### Alterando senha do usuario postgres no banco.. ####################"
        #altera a senha no banco do usuário postgres
        controle "su postgres -c \"psql -c \\\"ALTER ROLE postgres PASSWORD 'urb3mCNM';\\\" \" " 4
        echo "#################### Criando banco urbem.. ####################"
        controle "su postgres -c \"psql -c \\\"CREATE DATABASE urbem;\\\" \" " 4
        controle "su postgres -c \"psql -d urbem -c \\\"CREATE LANGUAGE plpgsql;\\\" \" " 4
        controle "su postgres -c \"psql -c \\\"CREATE ROLE urbem LOGIN PASSWORD 'nao_lembro_a_senha';\\\" \" " 4
        controle "su postgres -c \"psql -c \\\"CREATE ROLE siamweb LOGIN PASSWORD 'linuxx';\\\" \" " 4
        controle "su postgres -c \"psql -c \\\"CREATE ROLE \\\\\\\"sw.admin\\\\\\\" LOGIN SUPERUSER PASSWORD 'suporte';\\\" \" " 4
        controle "su postgres -c \"psql -c \\\"GRANT siamweb TO \\\\\\\"sw.admin\\\\\\\";\\\" \" " 4
        echo "#################### Rodando DUMP para criação das tabelas.. ####################"
        controle "su postgres -c \"psql -d urbem < $SW_ROOT/urbem.sql \" " 4
        controle "su postgres -c \"psql -d urbem -c \\\"UPDATE administracao.configuracao SET valor = '/var/www/urbem' WHERE parametro = 'diretorio';\\\" \" " 4

        echo "#################### Stop no banco para configuração.. ####################"
        #pausa o banco para efetuar modificações na configuração
        controle "./postgres stop" 4

        #altera de trust para md5, para solicitar senha ao se conectar no banco
        cd /usr/local/pgsql/data
        controle "su postgres -c \"mv pg_hba.conf pg_hba.conf.default\"" 4
        controle "su postgres -c \"sed 's/trust/md5/g' pg_hba.conf.default > pg_hba.conf \"" 4

        echo "#################### Iniciando banco novamente.. ####################"
        
        sleep 7

        #inicia o banco e adiciona o postgres na inicialização do sistema
        cd /etc/init.d/
        controle "./postgres start" 4

        echo "#################### Removendo arquivos de instalação.. ####################"
        #remove o tar e o diretório descompactado
        cd $SW_ROOT
        controle "rm postgresql-8.1.23/ -rf "
    else
        echo "#################### Postgres já está instalado! ####################"
    fi
}    

apache2(){
    metodo="apache"
    cd $SW_ROOT
    # alterar charset default no arquivo de configuração do apache
    controle "echo \"AddDefaultCharset UTF-8\" >> /etc/apache2/conf.d/charset"
    controle "echo \"AddType application/x-httpd-php .php\" >> /etc/apache2/mods-available/mime.conf"
    controle "service apache2 restart"
}


php5(){
    metodo="php"
    if [ ! -d /etc/php5 ]; then
        cd $SW_ROOT
        # Compilação e instalação do PHP 5.2
        controle "mkdir -p /etc/php5/apache2" 1
        controle "tar -jxvf php-5.2.17.tar.bz2" 1
        
        cd php-5.2.17

        controle "./configure --with-config-file-path=/etc/php5/apache2 --enable-cgi --enable-fastcgi --with-xmlrpc --enable-soap --enable-ftp --enable-calendar --enable-ctype --enable-pcntl --enable-session --with-regex=php --enable-spl --enable-zip --enable-sockets --with-gettext --enable-mbstring=all --enable-bcmath --with-apxs2=/usr/bin/apxs2 --with-gettext --with-pgsql --with-zlib --enable-ftp --enable-mbregex" 2
        
        controle "make" 2
        controle "make install" 2
        
        cd ..

        #instalação do Zend
        controle "tar -jxvf ZendOptimizer.tar.bz2" 3
        controle "mkdir -p /usr/local/Zend/lib/" 4
        controle "mv ZendExtensionManager.so ZendExtensionManager_TS.so ZendOptimizer.so ZendOptimizer_TS.so /usr/local/Zend/lib/" 4
        controle "cp php.ini /etc/php5/apache2/" 4

        controle "service apache2 restart" 4

        #remove o tar e o diretório descompactado
        cd $SW_ROOT
        controle "rm php-5.2.17 -rf"
    else
        echo "#################### PHP já está instalado! ####################"
    fi
}


tomcat(){
    metodo="tomcat"
    if [ ! -d /usr/local/tomcat-urbem ]; then
        cd $SW_ROOT
        #instalação do tomcat
        controle "tar -jxvf tomcat-urbem.tar.bz2" 1
        controle "mv tomcat-urbem /usr/local/" 2

        #inicia o tomcat
        controle "/usr/local/tomcat-urbem/bin/startup.sh" 2

    else
        echo "#################### Tomcat já está instalado! ####################"
    fi
}


configuracao(){
    metodo="configuracao"
    cd $SW_ROOT
    controle "mkdir /var/www/urbem" 1
    controle "cp config.yml /var/www/urbem" 1

    #adiciona o postgres na inicialização do sistema
    controle "echo \"/etc/init.d/postgres start\" >> /etc/init.d/rc.local"

    #adiciona o tomcat na inicialização do sistema
    controle "echo \"/usr/local/tomcat-urbem/bin/startup.sh\" >> /etc/init.d/rc.local"
}

finalizar(){
    echo "#################### A instalação foi concluída com sucesso! ####################"
}

downloads
postgres
apache2
php5
tomcat
configuracao
finalizar

cd $SW_ROOT/..
sh atualizador.sh
