<?php

$filename = "stars-hd-wallpaper-7.jpg";

$base64 = base64_encode(file_get_contents($filename));

$fileinfo =  new finfo(FILEINFO_MIME_ENCODING);

$mimetype = $fileinfo->file($filename);

$base64encode =  "data:".$mimetype.";base64,".$base64;


?>

<a href="<?=$base64encode?>" target="blank">Link para abrir imagem</a>

<img src="<?=$base64encode?>">


