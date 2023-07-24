<?php

ini_set( 'memory_limit', '1900M' );

function ifPng($file) {
	if (exif_imagetype($file) == IMAGETYPE_PNG) {
		return true;
	}
	return false;
}

function mmToPx($m) {
//	$dpi= 350;
	$dpi= 200; // poster.
	
	return ceil($dpi / 25.4 * $m);
}

//

$file = $argv[1];

if (! is_file($file)) {
	echo("ERROR: ファイルがありません\n");
	exit;
}

if (! ifpng($file)) {
	echo("ERROR: PNG画像ではありません\n");
	exit;
}

echo("LOADING: ".$file."\n");

$img = ImageCreateFromPNG($file);

$img_w = ImageSx($img);
$img_h = ImageSy($img);

//

$pos_w = mmToPx(1189); 
$pos_h = mmToPx(841);

// A0：841mm×1189mm
// A1：594mm×841mm
// A2：420mm×594mm
// B2:515mm×728mm

$nuri = mmToPx(3);

//

$img_a = ImageCreateTrueColor($pos_w, $pos_h);

ImageCopyResampled($img_a, $img,
	0,0,0,0, $pos_w, $pos_h, $img_w, $img_h);

//

$img = ImageCreateTrueColor($pos_w + $nuri * 2, $pos_h + $nuri * 2);

ImageCopyResampled($img, $img_a,
	0,0,0,0, $pos_w + $nuri * 2, $pos_h + $nuri * 2, $pos_w, $pos_h);

//

imagecopy($img, $img_a, $nuri, $nuri, 0, 0, $pos_w, $pos_h);

//

$filepath = pathinfo($file);

imagepng($img, $filepath['filename'].'_poster.'.$filepath['extension']);

//

imagedestroy($img);
imagedestroy($img_a);

//

echo("出力: ".$filepath['filename']."_poster.".$filepath['extension']."\n");
exit;
