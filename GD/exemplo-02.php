<?php

$image = imagecreatefromjpeg("080certificado.jpg");

$titlecolor = imagecolorallocate($image, 0, 0, 0);
$gray = imagecolorallocate($image, 100, 100, 100);

imagestring($image, 5, 450, 150, "CERTIFICADO", $titlecolor);
imagestring($image, 4, 440, 350, "Marcos Aurelio", $titlecolor);
imagestring($image, 4, 440, 370, utf8_decode( "Concluído em:".date("d-m-Y")), $gray);


header("Content-Type: image/jpeg");

imagejpeg($image);

imagedestroy($image);

?>