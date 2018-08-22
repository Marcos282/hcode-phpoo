<?php
include_once 'ini/config.inc.php';
include_once 'funcoes.php';
?>
<form action="" method="POST">
    Selecione a empresa:
    <select name="EMPRESA">
     
    </select>
    
    <input type="submit" name="OK" value="OK">
    
</form>

<?php
 
$sequencial_rem = "127"; //Sempre que for enviado é necessário acrescentar um número
$nr_sequencial  = 2; //Pegar do banco de dado
$carteira       = 009;



    
    $fusohorario = 3; 
    $timestamp = mktime(date("H") - $fusohorario, date("i"), date("s"), date("m"), date("d"), date("Y"));

    $DATAHORA['PT']     = gmdate("d/m/Y H:i:s", $timestamp);
    $DATAHORA['EN']     = gmdate("Y-m-d H:i:s", $timestamp);
    $DATA['PT']         = gmdate("d/m/Y", $timestamp);
    $DATA['EN']         = gmdate("Y-m-d", $timestamp);
    $DATA['DIA']        = gmdate("d",$timestamp);
    $DATA['MES']        = gmdate("m",$timestamp);
    $DATA['ANO']        = gmdate("y",$timestamp);
    $HORA               = gmdate("H:i:s", $timestamp);



    $filename = "RM_".$DATA['DIA'].$DATA['MES'].$DATA['ANO'].".rm";
    $filename = "/home/brasweb/public_html/remessa/files/".$pref_file."$filename";
    $conteudo = '';

    ## REGISTRO HEADER
                                                                                     #NOME DO CAMPO                     #TAMANHO                #POSICAO    #PICTURE
    $conteudo .= '0';                                                               //Ident do reg                      001                     001 001     N             
    $conteudo .= 1;                                                                 //Ident do arq remessa              001                     002 002     N
    $conteudo .= 'REMESSA';                                                         //Literal remessa                   007                     003 009     A
    $conteudo .= '01';                                                              //Codigo do servico                 002                     010 011     N
    $conteudo .= limit('COBRANCA',15);                                              //Literal servico                   015                     012 026     A
    $conteudo .= $codigo_cli;                                                       //Codigo da empresa                 020                     027 046     N
    $conteudo .= limit($sacador,30);                                                //Nome da empresa                   030                     047 076     A
    $conteudo .= 237;                                                               //Num bradesc na camara comp        003                     077 079     N
    $conteudo .= limit('BRADESCO',15);                                              //Nome do banco por extenso         015                     080 094     A
    $conteudo .= date("dmy");                                                       //Data da gravacao do arquivo       006                     095 100     N
    $conteudo .= complementoRegistro(8,"brancos");                                  //Branco                            008                     101 108     A
    $conteudo .= 'MX';                                                              //Identificao do sistema            002                     109 110     A    
 #   $conteudo .= sequencial_7($sequencial_rem);
    $conteudo .= limit_zero($sequencial_rem,7);//Nr sequencial da remessa          007                     111 117     N
    $conteudo .= complementoRegistro(277,"brancos");                                //Branco                            277                     118 394     A
    $conteudo .= sequencial_6(1);                                                   //Numero seq do registro de 1 a 1   006                     395 400     N
    $conteudo .= chr(13).chr(10); 

    $i = 2;
    $a = 1;

    predial2();
    $sql_clientes = pg_query($busca_sql);
    while($campo_cliente = pg_fetch_array($sql_clientes)){
        #$campo_cliente = iconv('UTF-8', 'ASCII//TRANSLIT', $campo_cliente);
        #$campo_cliente[nome] = remESP($campo_cliente[nome]);

        $campo_cliente[nome]            = ereg_replace("[^a-zA-Z0-9_]", " ", strtr($campo_cliente[nome], "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ", "aaaaeeiooouucAAAAEEIOOOUUC"));
        $campo_cliente[cpf]             = ereg_replace("[^a-zA-Z0-9_]", " ", strtr($campo_cliente[cpf], "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ", "aaaaeeiooouucAAAAEEIOOOUUC"));
        $campo_cliente[cobranca_rua]    = ereg_replace("[^a-zA-Z0-9_]", " ", strtr($campo_cliente[cobranca_rua], "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ", "aaaaeeiooouucAAAAEEIOOOUUC"));    


        $vencimento     = explode("-", $campo_cliente[data_vencimento]);
        $ano            = substr($vencimento[0], -2);
        $vencimento     = $vencimento[2].$vencimento[1].$ano;
        $nr_cliente_zero = limit_zero($campo_cliente[nr_cliente], 5); 
        $data_emissao = explode("/",$campo_cliente[data_documento]);
        $ano_emissao  = substr($data_emissao[2], -2);
        $mes_emissao  = $data_emissao[1];
        $data_emissao = $data_emissao[0].$data_emissao[1].$ano_emissao;

        #$documento =  $mes_emissao.$ano_emissao.$nr_cliente_zero;

        $valor          = str_replace(".", "", $campo_cliente[valor_documento]);
        print $nr_cliente_zero."-".$campo_cliente[nome]."-".$campo_cliente[cpf];

        if(empty($campo_cliente[cobranca_rua])){
            $campo_cliente[cobranca_rua] = "Rua sem nome,aa";
        }

        if(empty($campo_cliente[cobranca_cep])){
            $campo_cliente[cobranca_cep] = "24000000";
        }

        $prefixo_cep = soNumero(substr($campo_cliente[cobranca_cep],0,-3 ));
        $sufixo_cep = soNumero(substr($campo_cliente[cobranca_cep],5 ));

        
        #NOVA FORMACAO DO NOSSO NR# 20 ABR 2016
        $nosso_nr   = $ano_emissao.limit_zero($campo_cliente[nr_boleto], 9);
        #FIM NOVA FORMACAO NOSSO NR#
        
        #NOVA FORMACAO DO DOCUMENTO
        $documento = limit_zero($campo_cliente[nr_boleto], 10);
        #FIM NOVA FORMACAO DOCUMENTO
        
        ##########################################################################################################################################################
        //$nosso_nr = substr($campo_cliente[ano_referencia],-2) . $campo_cliente[mes_referencia] . substr($campo_cliente[ano_referencia],-2) . $nr_cliente_zero; #
        //$documento = $campo_cliente[mes_referencia] . substr($campo_cliente[ano_referencia],-2) . $nr_cliente_zero;                                            #
        //$documento = limit_zero($documento, 10);                                                                                                               #
        ##########################################################################################################################################################
        
        
        
        print "-".$nosso_nr."-".digito($nosso_nr)."<br>";  

        if(strlen($campo_cliente[cpf])==11){
            $gerar = true;
            $tipo_cliente = "01";
            $campo_cliente[cpf] = complementoRegistro(3, "zeros").$campo_cliente[cpf]; 
        }elseif(strlen($campo_cliente[cpf]) == 14){
            $tipo_cliente = "02";
            $gerar = true;
        }    

    
        ## REGISTRO DETALHE (OBRIGATORIO)
    if(($gerar === true)&&(!strlen($campo_cliente[cpf])< 11)){                                                                            #NOME DO CAMPO              #TAMANHO                #POSICAO    #PICTURE
        $conteudo .= 1;                                                         //Ident Registro                001                     001 001     N
        $conteudo .= complementoRegistro(5,"zeros");                            //Ag debito (op)                005                     002 006     N                                             
        $conteudo .= complementoRegistro(1,"zeros");                            //Dig ag d c/c (op)             001                     007 007     A
        $conteudo .= complementoRegistro(5,"zeros");                            //razao c/c (op)                005                     008 012     N
        $conteudo .= complementoRegistro(7,"zeros");                            //c/c (op)                      007                     013 019     N
        $conteudo .= complementoRegistro(1,"zeros");                            //Dig c/c (op)                  001                     020 020     A
        $conteudo .= "0" . "009" . $agencia . $conta;                           //Z Cart,Ag,c/c                 017                     021 037     A
        $conteudo .= complementoRegistro(25,"zeros");                           //Nr contr particip             025                     038 062     A
        $conteudo .= "000";                                                     //Cod banco deb au              003                     063 065     N
        $conteudo .= 2;                                                         //Multa                         001                     066 066     N
        $conteudo .= "0002";                                                    //Perc multa                    004                     067 070     N
        $conteudo .= $nosso_nr;                                                 //Ident tit banco               011                     071 081     N
        $conteudo .= digito($nosso_nr);                                         //Dig auto conf nr banca        001                     082 082     A
        $conteudo .= complementoRegistro(10,"zeros");                           //Desc bonif por dia            010                     083 092     N
        $conteudo .= 2;                                                         //Cond emiss papeleta de cob    001                     093 093     N
        $conteudo .= 'N';                                                       //Ident se emit bol deb auto    001                     094 094     N    
        $conteudo .= complementoRegistro(10,"brancos");                         //Ident da operacao do banco    010                     095 104     A
        $conteudo .= complementoRegistro(1,"brancos");                          //Ident rateio (op)             001                     105 105     A
        $conteudo .= 2;                                                         //End para aviso deb (op)       001                     106 106     N
        $conteudo .= complementoRegistro(2,"brancos");                          //Branco                        002                     107 108     A
        $conteudo .= "01";                                                      //Identif ocorrencia            002                     109 110     N
        $conteudo .= $documento;                                                //Nr do documento               010                     111 120     N
        $conteudo .= $vencimento; //vencimento                                  //Data de venc do titulo        006                     121 126     N    
        $conteudo .= limit_zero($valor, 13);                                    //Valor do titulo               013                     127 139     N
        $conteudo .= complementoRegistro(3,"zeros");                            //Banco encarreg da cob         003                     140 142     N
        $conteudo .= complementoRegistro(5,"zeros");                            //Agenc depositaria             005                     143 147     N
        $conteudo .= 12;                                                        //Especie de titulo             002                     148 149     N
        $conteudo .= 'N';                                                       //Identificacao                 001                     150 150     A
        $conteudo .= $data_emissao; // data de emissao                          //Data de emissao do titulo     006                     151 156     N
        $conteudo .= complementoRegistro(2,"zeros");                            //Primeira instrucao            002                     157 158     N
        $conteudo .= complementoRegistro(2,"zeros");                            //Segunda instrucao             002                     159 160     N
        $conteudo .= complementoRegistro(13,"zeros");                           //Valor a cobrar por dias atra  013                     161 173     N
        $conteudo .= $vencimento; //vencimento                                  //Data limite para desconto     006                     174 179     N
        $conteudo .= complementoRegistro(13,"zeros");                           //Valor do desconto             013                     180 192     N
        $conteudo .= complementoRegistro(13,"zeros");                           //Valor do IOF                  013                     193 205     N
        $conteudo .= complementoRegistro(13,"zeros");                           //Valor do abatim conced ou ca  013                     206 218     N
        $conteudo .= $tipo_cliente; //Verifcar clientes corporativos e prediais //Ident do tip insc pagador     002                     219 220     N    
        $conteudo .= limit($campo_cliente[cpf],14);  //BANCO DE DADOS           //Nr inscr pagador              014                     221 234     N
        $conteudo .= limit($campo_cliente[nome],40);  //BANCO DE DADOS          //Nome do pagador               040                     235 274     A
        $conteudo .= limit($campo_cliente[cobranca_rua],40);  //BANCO DE DADOS  //Endereço do pagador           040                     275 314     A 
        $conteudo .= limit('MENSAGEM1',12);                                     //Primeira mensagem             012                     315 326     A
        $conteudo .= limit_zero($prefixo_cep, 5);                               //Cep do pagador                005                     327 331     N    
        $conteudo .= limit_zero($sufixo_cep, 3);                                //Sufixo do CEP                 003                     332 334     N    
        $conteudo .= limit($campo_cliente[nome],60);                            //Sacador avalista              060                     335 394     A 
        $conteudo .= limit_zero($a++, 6);                                       //Sequencial registro           006                     395 400     N
        $conteudo .= chr(13).chr(10); //QUEBRA DE LINHA - FIM REGISTRO

        $nr_seq_ult =   $nr_seq_ult + 1;

        unserialize($vencimento);
        
    }
    }


    ## REGISTRO TRAILER DE ARQUIVO
                                                                                #NOME DO CAMPO                  #TAMANHO                #POSICAO    #PICTURE
        $conteudo .= 9;                                                             //Ident do reg                  001                     001 001     N
        $conteudo .= complementoRegistro(393,"brancos");                            //Brancos                       393                     002 394     A   
        $conteudo .= zeros($nr_seq_ult,6);                                          //Nr seq do ultimo registro     006                     395 400     N



    if (!$handle = fopen($filename, 'w+')) {
        
         erro("Não foi possível abrir o arquivo ($filename)");
    }
    
   
        if (fwrite($handle, "$conteudo") === FALSE) {
            erro("Não foi possível escrever no arquivo ($filename)");
        }
        
    
    fclose($handle);


}
?>