<?php

header("Content-Type: image/png");

$image = imagecreate(255, 100);

$black = imagecolorallocate($image, 0, 0, 0);

$red = imagecolorallocate($image, 255, 0, 0);

imagestring($image, 5, 25, 40, "CURSO DE PHP 7 DA HCODE", $red);

imagepng($image);

imagedestroy($image);

?>