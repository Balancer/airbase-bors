<?
    function print_top_navs($uri=NULL)
    {
        $ch = new Cache();
        if(0 && $ch->get('top_navs', $uri))
        {
            echo $ch->last();
            return;
        }

	    require_once("funcs/DataBaseHTS.php");
	    require_once("funcs/Cache.php");
    
	    $out = '';

        $hts = new DataBaseHTS();

        $GLOBALS['visited_pairs'] = array();

        $parents = link_line($uri);
        
//		echo $uri;
		
        if(!is_array($parents) || sizeof($parents)==0)
		{
			echo $hts->get_data($uri, 'nav_name');
            return;
		}
		
        sort($parents);
		
//		print_r($parents);
//		echo sizeof($parents);

        foreach($parents as $nav)
        {
            $links=split("#",$nav);
            $sum=array();
            foreach($links as $link)
            {   
                $name = $hts->get_data($link,'nav_name');
				
				if(strlen($name)>50)
					$name=substr($name,0,50)."...";
				
                if(!$name) 
					$name=$hts->get_data($link,'title', $link);
			
                $sum[] = $link != $uri ? "<a href=\"$link\">$name</a>" : $name;
            }
//            $sum=join(" &#187; ",$sum);
//            $out .= "&nbsp;&#183;&nbsp;$sum<br>";
            $out  = join(" / ",$sum);
        }

        echo $out;
        $ch->set('top_navs',$uri,$out);
    }

    function link_line($uri)
    {
        $hts = new DataBaseHTS();

//        echo "get links for '$uri'<br />";

        $parents = $hts->get_data_array($uri,'parent');
//        print_r($parents); echo " for $uri<br />";
        $links = array();

        foreach($parents as $parent)
        {
			$parent = $hts->normalize_uri($parent);
//			echo "get recursive links for $parent-$uri<br>";
            if(!isset($GLOBALS['visited_pairs']["$parent#$uri"]))
            {
                $GLOBALS['visited_pairs']["$parent#$uri"]=1;
                if($ret_parents = link_line($parent))
                {
                    foreach($ret_parents as $ret_parent)
                    {
                        $links[] = "$ret_parent#$uri";
//                        echo "pair = $pair<br>";
                    }
                }
                else
                    $links[] = "$parent#$uri";
            }
        }

        return $links;
    }
?>
