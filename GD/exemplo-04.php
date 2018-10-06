<?php

header("Content-type: image/jpeg");

$file = "wallpaper.jpg";

$new_width = 256;
$new_height = 256;

list($old_width,$old_heigth)= getimagesize($file);

$new_image = imagecreatetruecolor($new_height, $new_width);

$old_image = imagecreatefromjpeg($file);

imagecopyresampled($new_image, $old_image, 0, 0, 0,0, $new_width, $new_height, $old_width, $old_heigth);

imagejpeg($new_image);

imagedestroy($new_image);
imagedestroy($old_image);

?>