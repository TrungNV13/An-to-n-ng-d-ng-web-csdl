<?php
session_start();
$random_alpha = md5(rand());
$captcha_code = substr($random_alpha, 0, 6);
$_SESSION["captcha_code"] = $captcha_code;
$target_layer = imagecreatetruecolor(170, 70);
$captcha_background = imagecolorallocate($target_layer, 255, 255, 255);
imagefill($target_layer, 0, 0, $captcha_background);
$captcha_text_color = imagecolorallocate($target_layer, 39, 55, 70);
$font_size = 32;
$img_width = 80;
$img_height = 48;
$line_color = imagecolorallocate($target_layer, 64, 64, 64);
for ($i = 0; $i < 6; $i++) {
    imageline($target_layer, 0, rand() % 50, 200, rand() % 50, $line_color);
}
$pixel_color = imagecolorallocate($target_layer, 0, 0, 255);
for ($i = 0; $i < 1000; $i++) {
    imagesetpixel($target_layer, rand() % 200, rand() % 50, $pixel_color);
}
imagettftext($target_layer, $font_size, 0, 25, 35, $captcha_text_color, 'You are the one.ttf', $captcha_code);
header("Content-type: image/jpeg");
imagejpeg($target_layer);
