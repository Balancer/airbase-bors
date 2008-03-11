<?
    function print_top_navs($uri=NULL)
    {
//		echo "\n<!--Get nav for $uri-->\n";
	
        $ch = &new Cache();
        if($ch->get('top_navs-v7', $uri) && false)
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
//				echo "<!--$nav-->\n";
				$link_line = array();
				foreach(split("\|#\|", $nav) as $link)
				{	
					$obj = object_load($link);
					if($obj && $obj->nav_name())
					{
//						echo get_class($obj)."->".$obj->nav_name()."<br />\n";
						$url = $obj->url();
						$nav = $obj->nav_name();
					}
					else
					{
						$url = $link;
						$nav = $hts->get($link, 'nav_name');
//						echo "<b>***Nav for '$link' = '$nav'***</b><br/>";
					}
					
//					echo "{$link} -> {$obj->title()}<br />\n";
					$link_line[] = array('uri' => $url, 'title' => $nav);
//					echo "nav_name for $link = '".$obj->nav_name()."' ('$obj->stb_nav_name', '".$obj->title()."')<br />";
//					print_r($link_line);
				}
				
				$data[] = $link_line;
			}
		}
		
		unset($GLOBALS['module_data']);
		echo $ch->set(template_assign_data($tpl, array('links'=>$data)), -600);
		return;
    }

    function link_line($uri)
    {
//		echo "\n<!-- Link line for '$uri' -->\n";
		
		if($obj = class_load($uri))
	        $parents = $obj->parents();
		elseif(class_exists('DataBaseHTS'))
		{
//			echo "Can't load class '$uri'<br/>\n";
			$hts = &new DataBaseHTS($uri);
			$parents = $hts->get_array('parent');
		}
	
//		echo "\n<!--parents for $uri (".get_class($obj)."({$obj->id()})) = ".print_r($parents, true)."-->\n";
		$links = array();

		if(!is_array($parents))
		{
//			echo "Can't get parents for $uri<br />\n";
	        return $links;
		}
		
		foreach($parents as $parent)
        {
//			echo "Check '$parent' for '$uri'<br />\n";
			if($parent == $uri || $obj && $parent == $obj->url())
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
