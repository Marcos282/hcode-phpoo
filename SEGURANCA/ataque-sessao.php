<?php

session_start();

#Sempre depois de verificar login e senha reinicie o ID da sessão;
echo session_id();

session_destroy();

session_start();

session_regenerate_id();

echo session_id();

?>