<?php
	date_default_timezone_set("Europe/Vienna");

	// curl stuff
	$url = "http://www.dota2.com/international/compendium/";
	$ch = curl_init();
	$timeout = 5;
	
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$html = curl_exec($ch);
	curl_close($ch);
	
	// DOMDocument & xpath stuff
	$dom = new DOMDocument();
	@$dom->loadHTML($html);
	
	$xpath = new DOMXPath($dom);
	$result = $xpath->query("/html/body/div[@id='PrizePool']/div[@class='Content'][1]/div[@id='PrizePoolText']");
	
	// setting variables
    $prizepool = $result->item(0)->nodeValue;
	$timestamp = "Last Update: " . date("d.m. H:i") . " (GMT+2)";

	// re-write prizepool into int
	$prizeint = preg_replace("/[^0-9\.]/", "", $prizepool);
	$percentage = (substr($prizeint, 2, 7))/1000000;

	switch(true) {
		case $prizeint > 14000000:
			$last = imagecreatefrompng("images/weather.png");
			$preview = imagecreatefrompng("images/axe.png");
			$percentage_image = imagecreatefrompng("images/axe.png");
			break;
		case $prizeint > 13000000;
			$last = imagecreatefrompng("images/announcer.png");
			$preview = imagecreatefrompng("images/weather.png");
			$percentage_image = imagecreatefrompng("images/weather.png");
			break;
		case $prizeint > 12000000;
			$last = imagecreatefrompng("images/music.png");
			$preview = imagecreatefrompng("images/announcer.png");
			$percentage_image = imagecreatefrompng("images/announcer.png");
			break;
		case $prizeint > 11000000;
			$last = imagecreatefrompng("images/desert.png");
			$preview = imagecreatefrompng("images/music.png");
			$percentage_image = imagecreatefrompng("images/music.png");
			break;
		default:
			$last = imagecreatefrompng("images/treasure.png");
			$preview = imagecreatefrompng("images/desert.png");
			$percentage_image = imagecreatefrompng("images/desert.png");
			break;
	}

	// GD Stuff
	// font variables
	header("Content-type: image/png");
	$bg = imagecreatefrompng("images/dynheader.png");
	$im = imagecreatetruecolor(imagesx($bg), imagesy($bg));
	$color = imagecolorallocate($im, 178, 158, 116);
	$font = "fonts/GoudyTrajan.otf";
	$fontsize1 = 40;
	$fontsize2 = 16;

	// copy reward images
	if($prizeint <= 15000000) {
		$psize = ceil($percentage * imagesx($preview));
	} else { $psize = 210; }
	imagefilter($preview, IMG_FILTER_GRAYSCALE);
	imagefilter($preview, IMG_FILTER_BRIGHTNESS, -75);
	imagecopy($im, $bg, 0, 0, 0, 0, imagesx($bg), imagesy($bg));
	imagecopy($im, $last, 10, 20, 0, 0, 210, 160);
	imagecopy($im, $preview, 630, 20, 0, 0, 210, 160);
	imagecopy($im, $percentage_image, 630, 20, 0, 0, $psize, 160);

	// write stuff
	$imgwidth = imagesx($im);
	$tb1 = imagettfbbox(40, 0, $font, $prizepool);
	$x1 = ceil(($imgwidth - $tb1[2]) / 2);
	$tb2 = imagettfbbox(16, 0, $font, $timestamp);
	$x2 = ceil(($imgwidth - $tb2[2]) / 2);
	imagettftext($im, $fontsize1, 0, $x1, 155, $color, $font , $prizepool);
	imagettftext($im, $fontsize2, 0, $x2, 195, $color, $font , $timestamp);

	// create image, flush memory
	imagedestroy($bg);
	imagedestroy($last);
	imagedestroy($percentage_image);
	imagedestroy($preview);
	imagepng($im);
	imagedestroy($im);
?>
