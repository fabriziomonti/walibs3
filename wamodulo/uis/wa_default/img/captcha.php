<?php
#######################
#Captcha php
// trovato da Giuseppe, rimpastato da bicio
#######################

/*Apro la sessione*/
session_start();

/*Definisco l'immagine che verrà utilizzata come base per il captcha*/
$img = imagecreatefromjpeg(dirname(__FILE__) . "/base_captcha.jpg");

/*Definisco il colore del testo, in questo caso il bianco*/
$testo = imagecolorallocate($img, 255, 255, 255);

/*Definisco le dimensioni e le distanze dai bordi del testo*/
imagestring($img, 12, 12, 2, $_SESSION["WAMODULO_CODICE_CAPTCHA_$_GET[k]"], $testo);

header("Content-type: image/jpeg");

/*Visualizzo l'immagine*/
imagejpeg($img);
?>