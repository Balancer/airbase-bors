<?

    function show_keywords($page,$limit)
    {
        $ch = new Cache();
//        $ch->clear_check("forum_title_keywords:$limit", $page,900);
        if($ch->get("forum_title_keywords:$limit", $page))
        {
            echo $ch->last();
            return;
        }

        $out = NULL;

        $hts = &new DataBaseHTS();

        $keys = $hts->get_data_array($page,'keyword');

        $like_keys = array();

        foreach($keys as $key)
            $like_keys[] = " LOWER(`title`) LIKE '%".addslashes(strtolower($key))."%' ";

        $q = join(" OR ", $like_keys);

        if($q)
            $topics=$hts->dbh->get_array("SELECT * FROM FORUM.ib_topics WHERE ($q) AND `moved_to` IS NULL ORDER BY `posts` DESC LIMIT 0, ".intval($limit));

        if(!empty($topics))
        {
            $out .= "<dl class=\"box\"><dt>На форумах по теме</dt>\n<dd>\n";
            foreach($topics as $topic)
            {
                $out .= "<a href=\"http://forums.airbase.ru/index.php?showtopic={$topic['tid']}\" title=\"".addslashes($topic['description'])."\"><img src=\"/img/design/icons/topic-9x10.png\" width=\"9\" heght=\"10\" border=\"0\" align=\"absmiddle\">&nbsp;{$topic['title']}</a><br />\n";//&nbsp;&#183;&nbsp;
            }
            $out .= "</dd></dl>\n";
        }

        echo $out;
        $ch->set("forum_title_keywords:$limit", $page, $out);
        
    }
    
    global $page;

    if(isset($page) && empty($xpage))
        $xpage = $page;

    if(empty($xpage))
        return;

    if(empty($limit))
        $limit=20;

    show_keywords($xpage, $limit);
    unset($xpage, $limit);
?>
