<?
    function print_top_navs($uri=NULL)
    {
//		echo "Get nav for $uri<br />";
	
	    require_once("funcs/Cache.php");
        $ch = &new Cache();
        if($ch->get('top_navs-v6', $uri))
        {
			echo $ch->last();
			return;
        }

	    $out = '';

        $GLOBALS['visited_pairs'] = array();

        $parents = link_line($uri);

		$hts = &new DataBaseHTS();

		include_once("funcs/templates/assign.php");
		$tpl = "top-navs.html";
		if(!empty($GLOBALS['module_data']['template']))
			$tpl = $GLOBALS['module_data']['template'];
        
		$data = array();

        if(!is_array($parents) || sizeof($parents)==0)
		{
			$obj = class_load($uri);
			if($obj)
				$data[] = array(array('uri' => $uri, 'title' => $obj->nav_name()));
			else
				$data[] = array(array('uri' => $uri, 'title' => $hts->get($uri, 'nav_name')));
		}
		else
		{
	        sort($parents);
		
    	    foreach($parents as $nav)
			{
//				echo "$nav <br />";
				$link_line = array();
				foreach(split("\|#\|", $nav) as $link)
				{	
//					echo "$link<br />";
					$obj = class_load($link);
					$link_line[] = array(
						'uri' => preg_match('!^http://!', $link) ? $link : $obj->uri(),
						'title' => $obj ? $obj->nav_name() : $hts->get($link, 'nav_name'),
					);
//					echo "nav_name for $link = '".$obj->nav_name()."' ('$obj->stb_nav_name', '".$obj->title()."')<br />";
//					print_r($link_line);
				}
				
				$data[] = $link_line;
			}
		}
		
		unset($GLOBALS['module_data']);
		echo $ch->set(template_assign_data($tpl, array('links'=>$data)), 7200);
		return;
    }

    function link_line($uri)
    {
//		echo "Link line for '$uri'<br />\n";
		
		$obj = class_load($uri);
		if($obj)
	        $parents = $obj->parents();
		else
		{
//			echo "Can't load class '$uri'<br/>\n";
			$hts = &new DataBaseHTS($uri);
			$parents = $hts->get_array('parent');
		}
	
//		echo "parents for $uri = ".print_r($parents, true)."<br/>";
        $links = array();

        foreach($parents as $parent)
        {
//			echo "Check '$parent' for '$uri'<br />\n";
			if($parent == $uri || $obj && $parent == $obj->uri())
				continue;

            if(!isset($GLOBALS['visited_pairs']["$parent|#|$uri"]))
            {
                $GLOBALS['visited_pairs']["$parent|#|$uri"]=1;
                if($ret_parents = link_line($parent))
                {
                    foreach($ret_parents as $ret_parent)
                        $links[] = "$ret_parent|#|$uri";
                }
                else
                    $links[] = "$parent|#|$uri";
            }
        }

//		print_r($links); echo "<br /><br />";
        return $links;
    }
