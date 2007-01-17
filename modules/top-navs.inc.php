<?
    function print_top_navs($uri=NULL)
    {
        $ch = &new Cache();
        if($ch->get('top_navs', $uri))
        {
            echo $ch->last();
            return;
        }

	    require_once("funcs/DataBaseHTS.php");
	    require_once("funcs/Cache.php");
    
	    $out = '';

        $hts = &new DataBaseHTS();

        $GLOBALS['visited_pairs'] = array();

        $parents = link_line($uri);

		include_once("funcs/templates/assign.php");
		$tpl = "top-navs.html";
		if(!empty($GLOBALS['module_data']['template']))
			$tpl = $GLOBALS['module_data']['template'];
        
		$data = array();
        if(!is_array($parents) || sizeof($parents)==0)
		{
			$data[] = array(array('uri'=>$uri, 'title'=>$hts->get($uri, 'nav_name')));
		}
		else
		{
	        sort($parents);
		
    	    foreach($parents as $nav)
			{
				$link_line = array();
				foreach(split("\|#\|", $nav) as $link)
				{
					$link_line[] = array(
						'uri' => $link, 
						'title' => $hts->get($link, 'nav_name'),
					);
				}
				
				$data[] = $link_line;
			}
		}
		
		echo $ch->set(template_assign_data($tpl, array('links'=>$data)), 7200);
		return;
    }

    function link_line($uri)
    {
        $hts = &new DataBaseHTS();

        $parents = $hts->get_data_array($uri,'parent');
        $links = array();

        foreach($parents as $parent)
        {
			$parent = $hts->normalize_uri($parent);

			if($parent == $uri)
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

        return $links;
    }
