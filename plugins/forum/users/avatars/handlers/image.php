<?
//	print_r($GLOBALS['cms']['plugin_data']);

    register_handler("()av\.(png|jpg|gif)", 'cms_plugins_forum_user_avatar');

    function cms_plugins_forum_user_avatar($uri, $m, $plugin_data)
	{
//		echo config('avatar_dir');
	
		if(!preg_match("!/(\d+)/$!", $plugin_data['base_path'], $m))
			return false;
		
		$user_id = intval($m[1]);

		if(!$user_id)
			return false;

		if(!preg_match("!\.(png|gif|jpg)$!", $uri, $m))
			return false;

		$ext = $m[1];

		$avatar_source_file = config('avatar_dir')."/$user_id.$ext";

		if(!file_exists($avatar_source_file))
			return false;

		if($ext == 'jpg')
			$extf = 'jpeg';
		else
			$extf = $ext;

		$func1 = "imagecreatefrom$extf";
		$func2 = "image$extf";
			
		$img_src = $func1($avatar_source_file);


		$ww = 102;
		$hh = 170;

		$mx = $ww - 1;
		$my = $hh - 1;
		
		$img_dst = imagecreatetruecolor($ww, $hh);
		
		$white = imagecolorallocate($img_dst, 255, 255, 255);
		$grey  = imagecolorallocate($img_dst, 128, 128, 192);

		imagefill($img_dst, 0, 0, $white);
		
		imageline($img_dst,    0,  0, $mx,   0, $grey);
		imageline($img_dst, $mx,   0, $mx, $my, $grey);
		imageline($img_dst, $mx, $my,   0, $my, $grey);
		imageline($img_dst,   0, $my,   0,   0, $grey);
		imageline($img_dst,   0, $mx, $mx, $mx, $grey);
		
//		$hts = &new DataBaseHTS();

		$user = &new User($user_id);
		
		$user_nick = $user->get('nick');
		
		$text_color = imagecolorallocate($img_dst, 233, 14, 91);
//		imagestring($img_dst, 1, 5, $mx+5,  "A Simple Text String", $text_color);

		imagecopy($img_dst, $img_src, 1, 1, 0, 0, imagesx($img_src), imagesy($img_src));

		imagettftext($img_dst, 8, 0, 2, $mx+13, $grey, dirname(__FILE__)."/verdana.ttf", $user_nick);

		if($ext == 'jpg')
			$type = IMAGETYPE_JPEG;
		elseif($ext == 'gif')
			$type = IMAGETYPE_GIF;
		else
			$type = IMAGETYPE_PNG;

		header("Content-type: " . image_type_to_mime_type($type));
		echo $func2($img_dst);

//		imagepng($img_dst);

		imagedestroy($img_dst);
		imagedestroy($img_src);

		return true;
	}
