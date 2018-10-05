<?php

$data = array(
    "NOME"=>"Marcos Sousa",
    "CPF"=>"08187678712"
);

setcookie("DADOS_CLIENTE", json_encode($data), time() + 3600);
#NOTA QUE É OBRIGADO A INFORMAR O TERCEIRO PARAMETRO (TEMPO DE EXPIRAÇÃO).  POIS
#SEM ELE O NAVEGADOR NÃO GRAVA NO COMPUTADOR DO USUARIO

echo "ok";

?>