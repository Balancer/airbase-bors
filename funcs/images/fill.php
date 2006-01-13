<?
    require_once('funcs/DataBaseHTS.php');
    require_once('funcs/users.php');
    require_once('funcs/filesystem_ext.php');

    function fill_image_data($image, $page = NULL)
    {
        if(substr($page,-1)!='/')
            $page = dirname ($page) . '/';

        $image2 = abs_path_from_relative($image, "{$page}img/");
		
        $hts = new DataBaseHTS();

		$data = $hts->parse_uri($page);
//		exit($image2.print_r($data, true));
		
//		exit("'$image2' = abs_path_from_relative('$image', '{$page}img');");

        if(!file_exists(preg_replace("!http://{$data['host']}!",$data['root'],$image2)))
            $image2 = abs_path_from_relative($image, $page);
		
//		exit("'$image2' = abs_path_from_relative('$image', '{$page}img')");

        if(file_exists(preg_replace("!http://{$data['host']}!",$data['root'],$image2)))
            $image = $image2;

//		exit(preg_replace("!http://{$data['host']}!",$data['root'],$image2));

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