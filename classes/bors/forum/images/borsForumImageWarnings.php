<?
	require_once('classes/bors/borsImageGIF.php');

	class borsForumImageWarnings extends borsImageGIF
	{
		function class_name() { return 'forum/images/borsForumImageWarnings'; }

		function make_image()
		{
			$user_id = $this->id();
			
			if($user_id)
			{
				$db = &new DataBase('punbb');
				$warn_count = intval($db->get("SELECT COUNT(*) FROM warnings WHERE user_id = $user_id AND time > ".(time()-30*86400)));
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
		
			$cross_full = imagecreatefromgif("/var/www/balancer.ru/htdocs/img/web/cross.gif");
			$cross_half = imagecreatefromgif("/var/www/balancer.ru/htdocs/img/web/cross-half.gif");
		
			$sx = imagesx($cross_full);
			$sy = imagesy($cross_full);

			$cross_full_count = intval($warn_count / 2);
			$cross_half_count = $warn_count % 2;
		
			for($i=0; $i<$cross_full_count; $i++)
				imagecopy($img, $cross_full, 10+$i*$sx, 0, 0, 0, $sx, $sy);

			if($cross_half_count)
				imagecopy($img, $cross_half, 10+$cross_full_count*$sx, 0, 0, 0, $sx, $sy);

			$path = "/var/www/balancer.ru/htdocs/user/$user_id";
			include_once("funcs/filesystem_ext.php");
			mkpath($path, 0775);
			imagegif($img, "$path/warnings.gif");
			chmod("$path/warnings.gif", 0664);

			imagedestroy($img);
			imagedestroy($cross_full);
			imagedestroy($cross_half);
			
			return "http://balancer.ru/user/$user_id/warnings.gif";
		}
	}
