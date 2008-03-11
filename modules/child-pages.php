<style>
ul.children{margin: 0 0 0 16px; padding: 0px; font-size: xx-small; font-family: sans-serif;}
li.folder{margin-bottom: 2px; list-style-image: URL("/img/design/icons/folder-12x10.png");}
li.document{margin-bottom: 2px; list-style-image: URL("/img/design/icons/document-8x10.png");}
</style>
<?

//    require_once("/home/airbase/html/inc/config.php");

    function print_child_pages($uri=NULL, $limit, $depth=1)
    {
        if(!$uri)
            $uri="http://{$_SERVER['HTTP_HOST']}{$GLOBALS['REQUEST_URI']}";

        $ch = new Cache();
        if($ch->get('child_pages',$uri))
        {
            echo $ch->last();
            return;
        }

        $hts = &new DataBaseHTS();
        $out = NULL;

        $children = get_children($uri, $limit*3, $depth, true);

        if($children)
        {
            $child_name = $hts->get_data($uri,'nav_name');
//            echo "<h5>Подразделы <a href=\"$child_uri\">$child_name</a></h5>\n";
            $out .= "<dl class=\"box\"><dt>Дочерние ресурсы</dt><dd>\n";
            $out .= "<ul class=\"children\">\n";
            $out .= $children;
            $out .= "</ul></dd></dl>\n";
        }

        echo $out;
        $ch->set('child_pages',$uri,$out);
    }

    function get_children($uri, $limit, $depth, $child_image_show=false)
    {
        $hts = &new DataBaseHTS();

        $limit = max(intval($limit/3),3);

        $child_name=$hts->get_data($uri,'nav_name');
//        echo "get children for $child_name ($id)<br />";

//        $GLOBALS['log_level']=9;
        $children=$hts->get_data_array($uri,'child');
//        $GLOBALS['log_level']=2;

//        print_r($children);

        if($depth<0)
            return $children;

//        print_r($children);
        $tmp = array();
        foreach($children as $child)
        {
            $child_name = $hts->get_data($child,'nav_name');
            if(!$child_name)
                $child_name=$hts->get_data($child,'h1');
            if(!$child_name && $child_image_show)
                $child_name="◄$child";
            if($child_name)
                $tmp[$child] = $child_name;
        }

        asort($tmp,SORT_STRING);

        $links='';
        $links_number = 0;
        foreach($tmp as $child => $child_name)
        {
            if($links_number==$limit)
            {
                $links.="<li class=\"folder\">&nbsp;&nbsp;&nbsp;...\n";
                $links_number++;
            }

//            echo "id for '$child_id' = ";
//            echo "$child_id<br />";

            if($links_number<$limit && !isset($GLOBALS['linked_child'][$child]))
            {

                $links_number++;

                $GLOBALS['linked_child'][$child]=1;

//                $child_name=$hts->get_data($child_id,'nav_name');
                
                $ret_children=get_children($child,$limit,$depth-1);

                if($ret_children)
                    $links.="<li class=\"folder\"><a href=\"$child\">$child_name</a>\n";
                else
                    $links.="<li class=\"document\"><a href=\"$child\">$child_name</a>\n";

                if($depth)
                {
                    if($ret_children)
                    {
                        $links.="<ul class=\"children\">\n";
                        $links.=$ret_children;
                        $links.="</ul>\n";
                    }
//                    else
//                        $links.="<li class=\"document\"><a href=\"$child_uri\">$child_name</a>\n";
                }
            }
        }

        return $links;
    }

    global $page;

    if(isset($page) && empty($xpage))
        $xpage = $page;

    if(empty($xpage))
        return;

    if(empty($limit))
        $limit=16;

    if(empty($depth))
        $depth=1;

//    echo "page=$page<br>$xpage<br />";

    print_child_pages($xpage,$limit,$depth);
    unset($xpage, $limit);
?>
