<?
    require_once("funcs/DataBaseHTS.php");
    require_once("funcs/Cache.php");

    function print_top_navs($uri=NULL)
    {
        if(!$uri)
            $uri="http://{$_SERVER['HTTP_HOST']}{$GLOBALS['REQUEST_URI']}";

        $ch = new Cache();
        if($ch->get('top_navs', $uri))
        {
            echo $ch->last();
            return;
        }


        $out = '';

        $hts = new DataBaseHTS();

        $GLOBALS['visited_pairs']=array();

        $parents = link_line($uri);
        
        if(!is_array($parents))
            return;

        sort($parents);

        foreach($parents as $nav)
        {
            $links=split(":::",$nav);
            $sum=array();
            foreach($links as $link)
            {   
                $name=$hts->get_data($link,'nav_name');
                if(!$name) $name=$hts->get_data($link,'title', $link);
                $sum[]="<a href=\"$link\">$name</a>";
            }
            $sum=join(" &#187; ",$sum);
            $out .= "&nbsp;&#183;&nbsp;$sum<br>";
        }

        echo $out;
        $ch->set('top_navs',$uri,$out);
    }

    function link_line($uri)
    {
        $hts = new DataBaseHTS();

//        echo "get links for '$uri'<br>";

        $parents = $hts->get_data_array($uri,'parent');
//        print_r($parents);
        $links = array();

        foreach($parents as $parent)
        {
//            echo "get recursive links for $parent_id<br>";
            if(!isset($GLOBALS['visited_pairs']["$parent:::$uri"]))
            {
                $GLOBALS['visited_pairs']["$parent-$uri"]=1;
                if($ret_parents=link_line($parent))
                {
                    foreach($ret_parents as $ret_parent)
                    {
                        $pair=$ret_parent.":::".$parent;
                        $links[]=$pair;
//                        echo "pair = $pair<br>";
                    }
                }
                else
                    $links[]=$parent;
            }
        }

        return $links;
    }


    global $page;
    global $uri;

    if(isset($uri) && empty($xpage))
        $xpage = $uri;

    if(isset($page) && empty($xpage))
        $xpage = $page;

    if(empty($xpage))
        return;

    print_top_navs($xpage);
    unset($xpage);

//http://airbase.ru/hangar/planes/russia/su/su-27/
?>
