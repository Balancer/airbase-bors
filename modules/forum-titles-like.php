<?php
    require_once("funcs/DataBaseHTS.php");
    require_once("funcs/Cache.php");

    function show_titles_like($title,$limit=20, $forum=0)
    {
		return "";
	
        $ch = new Cache();

        $title = preg_replace("![^\w\-А-Яа-я]+!u", " ", $title);
        $title = preg_replace("!\s+!", " ", $title);
        $title = explode(" ", $title);
        sort($title);

        $stitle = join(" ", $title);

//        $ch->clear_check("forum_titles_like:$limit", $stitle,86400+rand(0,86400));
        if($ch->get("forum_titles_like:$limit", $stitle))
            return $ch->last();

        $out = NULL;

        $hts = new DataBaseHTS();

        $weights = array();

        if($forum == 0 && !empty($GLOBALS['ibforums']))
        {
        	$forum = $GLOBALS['ibforums']->input['f'];
        }

        foreach($title as $word)
        {
            if(strlen($word) <= 2)
				continue;

        	$ch->clear_check("forum_titles_with_key", $word, 86400+rand(0,86400));
        	if($ch->get("forum_titles_with_key", $word))
        	{
            	$topics = unserialize($ch->last());
	        	$count = $ch->get("forum_titles_with_key_count", $word);
			}
			else
			{
//			    $GLOBALS['log_level']=9;
				$topics =  $hts->dbh->get_array("SELECT `tid`, `posts`, `description`, `title`, `forum_id` FROM FORUM.ib_topics WHERE LOWER(`title`) LIKE '% ".addslashes(strtolower($word))." %'  AND `moved_to` IS NULL ORDER BY `posts` DESC LIMIT 0, ".intval($limit));
//			    $GLOBALS['log_level']=2;
				$topics += $hts->dbh->get_array("SELECT `tid`, `posts`, `description`, `title`, `forum_id` FROM FORUM.ib_topics WHERE LOWER(`title`) LIKE '% ".addslashes(strtolower($word))."%'  AND `moved_to` IS NULL ORDER BY `posts` DESC LIMIT 0, ".intval($limit));
				$topics += $hts->dbh->get_array("SELECT `tid`, `posts`, `description`, `title`, `forum_id` FROM FORUM.ib_topics WHERE LOWER(`title`) LIKE '%".addslashes(strtolower($word))."%'  AND `moved_to` IS NULL ORDER BY `posts` DESC LIMIT 0, ".intval($limit));

				$count = $hts->dbh->get("SELECT count(`tid`) FROM FORUM.ib_topics WHERE LOWER(`title`) LIKE '%".addslashes(strtolower($word))."%'  AND `moved_to` IS NULL ORDER BY `posts` DESC LIMIT 0, ".intval($limit));

	        	$ch->set("forum_titles_with_key", $word, serialize($topics));
	        	$ch->set("forum_titles_with_key_count", $word, $count);
		 	}

		 	$n=1;
			foreach($topics as $t)
			{
			    if(empty($weights[$t['tid']]))
			    	$weights[$t['tid']] = 0;

			    $w = log($t['posts'])/($n++ + sqrt($count)) + 1;
			    if($forum == $t['forum_id'])
				    $w += 4;

				$weights[$t['tid']] += intval($w*1000);
				$topics_info[$t['tid']] = $t;
			}
		}

		arsort($weights);

        if(!empty($weights) && !empty($topics))
        {
//            $out .= "<dl class=\"box\"><dt>Похожие темы форума</dt>\n<dd>\n";
            $out .= "<b>Похожие заголовки форума</b><br />\n";
            $n = 0;
            foreach($weights as $tid => $w)
            {
            	if($n<$limit)
				{
	                $t = $topics_info[$tid];
	                if(!preg_match("!^From:!", $t['title']))
	                {
	                	$n++;
	    	            $out .= "<a href=\"http://forums.airbase.ru/index.php?showtopic={$t['tid']}\" title=\"".addslashes($t['description'] . " [{$w}]" )."\"><img src=\"http://airbase.ru/img/design/icons/topic-9x10.png\" width=\"9\" heght=\"10\" border=\"0\" align=\"absmiddle\">&nbsp;{$t['title']}</a><br />\n";//&nbsp;&#183;&nbsp;
	    	       	}
				}
            }
//            $out .= "</dd></dl>\n";
        }

        $ch->set("forum_titles_like:$limit", $stitle, $out);
        return $out;
    }
    
//    echo show_titles_like("Параметры ракеты Р-27", 20, 3);
?>
