<?
    require_once('obsolete/DataBaseHTS.php');
    require_once('obsolete/users.php');
    require_once('inc/filesystem.php');

    function fill_image_data($image, $page = NULL)
    {
        if(substr($page,-1)!='/')
            $page = dirname ($page) . '/';

        $image2 = abs_path_from_relative($image, "{$page}img/");
		
        $hts = new DataBaseHTS();

		$data = url_parse($image2);

		if(!$data['local'])
		{
			$path = config('sites_store_path')."/{$data['host']}{$data['path']}";
				
			if(preg_match("!/$!",$path))
				$path .= "index";

			if(!file_exists($path) || filesize($path)==0)
			{
				$c1 = substr($data['host'],0,1);
				$c2 = substr($data['host'],1,1);
//				require_once('inc/uris.php');
				$path = config('sites_store_path')."/$c1/$c2/{$data['host']}".translite_path($data['path']);

				if(preg_match("!/$!",$path))
					$path .= "index";
			}

			if(!file_exists($path) || filesize($path)==0)
			{
				require_once('HTTP/Request.php');
				$req =& new HTTP_Request($image, array(
					'allowRedirects' => true,
					'maxRedirects' => 3,
					'timeout' => 10,
				));
				
				$req->addHeader('Referer', $image);

//				if(preg_match("!(lenta\.ru|pisem\.net|biorobot\.net|compulenta\.ru|ferra\.ru)!", $image))
//					$req->setProxy('home.balancer.ru', 3128);

//				return "=$path=<br />\n";
					
				$response = $req->sendRequest();

				if(!empty($response) && PEAR::isError($response)) 
					return "Download image =$image= error: ".$response->getMessage();

				$data = $req->getResponseBody();
				if(strlen($data) <= 0)
					return "Zero size image '{$image}' error.";

				$content_type = $req->getResponseHeader('Content-Type');
				if(!preg_match("!image!",$content_type))
					return "Non-image content type ('$content_type') image '{$image}' error.";

				require_once('inc/filesystem.php');
				mkpath(dirname($path));
				$fh = fopen($path,'wb');
				fwrite($fh, $data);
				fclose($fh);
//				$cmd = "wget --header=\"Referer: $image\" -O \"$path\" \"".html_entity_decode($image, ENT_COMPAT, 'UTF-8')."\"";
//				return "cmd:$cmd=<br />\n";
//				system($cmd);
			}

			if(file_exists($path) && filesize($path)>0)
			{
				$remote = $image;
				$image = str_replace(config('sites_store_path'), config('sites_store_uri'), $path);
				$data['local'] = true;
				if(!$hts->get_data($image,'origin_uri'))
					$hts->set_data($image, 'origin_uri', $remote);
				if(!$hts->get_data($image,'local_path'))
					$hts->set_data($image, 'local_path', $path);
			}

			$image2 = $image;
		}

		$data = url_parse($page);

        if(!file_exists(preg_replace("!http://{$data['host']}!",$data['root'], $image2)))
            $image2 = abs_path_from_relative($image, $page);
		
        if(file_exists(preg_replace("!http://{$data['host']}!",$data['root'],$image2)))
            $image = $image2;

        if(!file_exists(preg_replace("!http://{$data['host']}!",$data['root'],$image)))
            return false;
            
        if(!$hts->get_data($image, 'create_time'))
            $hts->set_data($image, 'create_time', time());

        if(!$hts->get_data($image, 'width') || !$hts->get_data($image, 'height') || !$hts->get_data($image, 'size'))
        {
            $parse = $hts->parse_uri($image);
            list($width, $height, $type, $attr) = @getimagesize($parse['local'] ? $parse['local_path'] : $image);
            $hts->set_data($image, 'width' , $width );
            $hts->set_data($image, 'height', $height);
            $hts->set_data($image, 'type'  , $type  );
            if($path = $hts->get_data($image, 'local_path'))
                $hts->set_data($image, 'size'  , filesize($path));
        }

        if(!$hts->get_data($image, 'author'))
            $hts->set_data($image, 'author', user_data('member_id'));

        $hts->set_data($image, 'modify_time', time());

        return $image;
    }
?>