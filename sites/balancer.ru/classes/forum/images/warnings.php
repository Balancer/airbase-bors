<?php

class forum_images_warnings extends bors_image_gif
{
	private $expired = NULL;

	function auto_objects()
	{
		return array(
			'user' => 'balancer_board_user(id)',
		);
	}

    function image()
	{
		$image_name = "skull"; // "cross"

		$user_id = $this->id();
		$user = $this->user();

		if($user_id)
		{
//			$db = new driver_mysql('AB_FORUMS');
//			$warn_count = min(10, intval($db->get("SELECT SUM(score) FROM warnings WHERE user_id = $user_id AND time > ".(time()-WARNING_DAYS*86400))));
			$warn_count = $user->warnings();
		}
		else
			$warn_count = 0;

		$func1 = "imagecreatefromgif";
		$func2 = "imagegif";

		$ww = 100;
		$hh = 16;

		$img = imagecreatetruecolor($ww, $hh);

		$transparent = imagecolorallocate($img, 255,99,140);
	    imagecolortransparent($img, $transparent);

		imagefill($img, 0, 0, $transparent);

		$cross_full = imagecreatefromgif("/var/www/balancer.ru/htdocs/img/web/{$image_name}.gif");
		$cross_half = imagecreatefromgif("/var/www/balancer.ru/htdocs/img/web/{$image_name}-half.gif");

		$sx = imagesx($cross_full);
		$sy = imagesy($cross_full);

		$cross_full_count = intval($warn_count / 2);
		$cross_half_count = $warn_count % 2;

		for($i=0; $i<$cross_full_count; $i++)
			imagecopy($img, $cross_full, 10+$i*$sx, 0, 0, 0, $sx, $sy);

		if($cross_half_count)
			imagecopy($img, $cross_half, 10+$cross_full_count*$sx, 0, 0, 0, $sx, $sy);

//		$warn_count = 10; // -----------------

		if($warn_count >= 10 || $user->is_banned())
		{
			$db = new driver_mysql('AB_FORUMS');
			$total = 0;
			$time  = 0;
			foreach($db->get_array("SELECT score, time FROM warnings WHERE user_id = {$user_id} ORDER BY time DESC LIMIT 20") as $w)
			{
				$total += $w['score'];
				if($total >= 10)
				{
					$time = $w['time'];
					break;
				}
			}

			$font = '/var/www/repos/bors-third-party/fonts/verdana.ttf';
			$red   = imagecolorallocate($img, 255,   0,   0);
			$black = imagecolorallocate($img,   0,   0,   0);
			$white = imagecolorallocate($img, 255, 255, 255);

			if(is_object($user->is_banned()))
				$text = ec('        админбан    ');
			else
				$text = ec('бан до '.strftime("%d.%m.%Y", $this->expired = $time+WARNING_DAYS*86400));
//			$text = ec('бан до '.strftime("%d.%m.%Y", time() + rand(3*86400, 365*10*86400)));

			$x = 0;
			$y = 8;
			$dd = 3;
			$angle = -3.2;
			$size = 7;

			for($ix=$x-$dd; $ix<=$x+$dd; $ix++)
				for($iy=$y-$dd; $iy<=$y+$dd; $iy++)
					imagettftext($img, $size, $angle, $ix, $iy, $white, $font, $text);

			imagettftext($img, $size, $angle, $x, $y, $red,   $font, $text);
		}

		ob_start();
		imagegif($img);
		$result = ob_get_contents();
		ob_end_clean();

		imagedestroy($img);
		imagedestroy($cross_full);
		imagedestroy($cross_half);

		return $result;
	}

	function url() { return "http://www.balancer.ru/user/{$this->id()}/warnings.gif"; }

	function cache_static() { return $this->expired && config('static_forum') ? $this->expired - time() : 86400; }

	function cache_groups() { return "user-{$this->id()}-warnings"; }
}
