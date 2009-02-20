<?php

class user_image_reputation extends base_image_gif
{
    function image()
	{
		$func1 = "imagecreatefromgif";
		$func2 = "imagegif";

		$ww = 100;
		$hh = 16;

		$img = imagecreatetruecolor($ww, $hh);
		
		$white = imagecolorallocate($img, 255, 255, 255);
		$grey  = imagecolorallocate($img, 128, 128, 192);

		imagefill($img, 0, 0, $white);
		
		$db = &new DataBase('punbb');

		$reputation_value = $db->get("SELECT reputation FROM users WHERE id = ".intval($this->id()));
		
		$reputation_abs = intval(0.99 + 20*atan($reputation_value*$reputation_value/200)/pi())/2;

		if($reputation_value > 0)
		{
			$star  = imagecreatefromgif("/var/www/balancer.ru/htdocs/img/web/star.gif");
			$star_half  = imagecreatefromgif("/var/www/balancer.ru/htdocs/img/web/star-half.gif");
		}
		else
		{
			$star = imagecreatefromgif("/var/www/balancer.ru/htdocs/img/web/bstar.gif");
			$star_half  = imagecreatefromgif("/var/www/balancer.ru/htdocs/img/web/bstar-half.gif");
		}
		
		$sx = imagesx($star);
		$sy = imagesy($star);
		
		if($reputation_abs)
		{
			for($i=0; $i<intval($reputation_abs); $i++)
				imagecopy($img, $star, 10+$i*$sx, 0, 0, 0, imagesx($star), imagesy($star));

			if($reputation_abs != intval($reputation_abs))
				imagecopy($img, $star_half, 10+intval($reputation_abs)*$sx, 0, 0, 0, imagesx($star), imagesy($star));
		}

		ob_start();
		imagegif($img);
		$result = ob_get_contents();
		ob_end_clean();

		imagedestroy($img);
		imagedestroy($star);
		imagedestroy($star_half);

		return $result;
	}
	
	function url() { return "http://balancer.ru/user/{$this->id()}/rep.gif"; }
	
	function cache_static() { return rand(3600*10, 3600*30); }
	
	function cache_groups() { return "user-{$this->id()}-reputation"; }
}
