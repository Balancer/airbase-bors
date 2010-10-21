<?php

class user_image_reputation extends base_image_gif
{
    function show_image()
	{
		$func1 = "imagecreatefromgif";
		$func2 = "imagegif";

		$db = new DataBase('punbb');
		$reputation_value = $db->get("SELECT reputation FROM users WHERE id = ".intval($this->id()));

		if($reputation_value >= 0)
		{
			$star  = imagecreatefromgif("/var/www/balancer.ru/htdocs/img/web/stars/2/star.gif");
			$star_empty  = imagecreatefromgif("/var/www/balancer.ru/htdocs/img/web/stars/star-empty.gif");
		}
		else
		{
			$star = imagecreatefromgif("/var/www/balancer.ru/htdocs/img/web/stars/2/star-black.gif");
			$star_empty  = imagecreatefromgif("/var/www/balancer.ru/htdocs/img/web/stars/star-black-empty.gif");
		}

		$sx = imagesx($star);
		$sy = imagesy($star);

		$ww = 100;
		$hh = $sy;

		$offset = intval((100 - $sx*5)/2);

		$min_rep = 8;
		$reputation_abs = intval($min_rep + 0.9 + ($sx*5-$min_rep)*atan($reputation_value*$reputation_value/($reputation_value >= 0 ? 300 : 100))*2/pi());

		$img  = imagecreatetruecolor($ww, $hh);
		$img_filled = imagecreatetruecolor($ww, $hh);

		$white = imagecolorallocate($img, 255, 255, 255);
		$grey  = imagecolorallocate($img, 128, 128, 192);

		$transparent = imagecolorallocate($img, 255,99,140);
	    imagecolortransparent($img, $transparent);

		imagefill($img, 0, 0, $transparent);
		imagefill($img_filled, 0, 0, $transparent);

		// Заполняем пустыми звёздами
//		for($i=0; $i<5; $i++)
//			imagecopy($img, $star_empty, $offset + $i*$sx, 0, 0, 0, imagesx($star), imagesy($star));

		// Заполняем полными звёздами
		for($i=0; $i<5; $i++)
			imagecopy($img_filled, $star, $offset + $i*$sx, 0, 0, 0, imagesx($star), imagesy($star));

		if($reputation_abs)
			imagecopy($img, $img_filled, 0, 0, 0, 0, $offset + $reputation_abs, 20);

		imagegif($img);

		imagedestroy($img);
		imagedestroy($star);
		imagedestroy($star_empty);
	}

	function url() { return "http://balancer.ru/user/{$this->id()}/rep.gif"; }

	function cache_static() { return rand(3600*10, 3600*30); }

	function cache_groups() { return "user-{$this->id()}-reputation"; }
}
