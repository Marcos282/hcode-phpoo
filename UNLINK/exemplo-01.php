<?php

$file = fopen("arquivo.txt", "a+");

fclose($file);

unlink("arquivo.txt");

echo "arquvio removido com sucesso!";

?>