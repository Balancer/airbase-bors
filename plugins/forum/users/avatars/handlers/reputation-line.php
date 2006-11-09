<?
    register_handler("\.gif", 'cms_plugins_forum_user_reputation_line');

    function cms_plugins_forum_user_reputation_line($uri, $m, $plugin_data)
	{
		if(!preg_match("!/(\d+)/$!", $plugin_data['base_path'], $m))
			return false;
		
		$user_id = intval($m[1]);

		if(!$user_id)
			return false;

		$func1 = "imagecreatefromgif";
		$func2 = "imagegif";
			

		$ww = 100;
		$hh = 16;

		$img = imagecreatetruecolor($ww, $hh);
		
		$white = imagecolorallocate($img, 255, 255, 255);
		$grey  = imagecolorallocate($img, 128, 128, 192);

		imagefill($img, 0, 0, $white);
		
		$db = &new DataBase('punbb');

		$reputation_value = $db->get("SELECT reputation FROM users WHERE id = $user_id");
		
		$reputation_abs = intval(0.9 + 20*atan(abs($reputation_value)/4)/pi())/2;

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
				imagecopy($img, $bstar, 10+intval($reputation_abs)*$sx, 0, 0, 0, imagesx($star), imagesy($star));
		}

		header("Content-type: " . image_type_to_mime_type(IMAGETYPE_GIF));

		$path = "/var/www/balancer.ru/htdocs/user/$user_id";
		include_once("funcs/filesystem_ext.php");
		mkpath($path, 0775);
		imagegif($img, "$path/rep.gif");
		chmod("$path/rep.gif", 0664);
		readfile("$path/rep.gif");

		imagedestroy($img);
		imagedestroy($star);
		imagedestroy($star_half);

		return true;
	}
