<?
	function index_body($page_id, $text)
	{
		$text = trim($text);
		$page_id = intval($page_id);
		
		if(!$text || !$page_id)
			return;

		include_once("include/classes/text/Stem_ru.php");
			
		$db = new DataBase('SEARCH');

		$Stemmer = new Lingua_Stem_Ru();
				
		$words = index_split($text);
		
		for($i=0; $i<10; $i++)
			$db->query("DELETE FROM body_$i WHERE page_id = $page_id");
		
		foreach($words as $word)
		{
			if(!$word)
				continue;
				
			$word = $Stemmer->stem_word($word);
			
			if(strlen($word) > 16)
				$word = substr($word, 0, 16);
				
			$id = $db->get("SELECT id FROM words WHERE word = '".addslashes($word)."'");
			if(!$id)
			{
				$db->insert('words', array('word' => $word));
				$id = $db->last_id();
			}

			$sub = $id%10;
			
			$count = intval($db->get("SELECT count FROM body_$sub WHERE word_id = $id AND page_id = $page_id"));
			if(!$count)
				$db->replace("body_$sub", array('word_id'=>$id, 'page_id'=>$page_id, 'count'=>1));
			else
				$db->update("body_$sub", "word_id = $id AND page_id = $page_id", array('count' => $count+1));
		}
	}

	function index_title($page_id, $text, $clean=true)
	{
		$text = trim($text);
		$page_id = intval($page_id);
		
		if(!$text || !$page_id)
			return;

		include_once("include/classes/text/Stem_ru.php");
			
		$db = new DataBase('SEARCH');

		$Stemmer = new Lingua_Stem_Ru();
				
		$words = index_split($text);
		
		if($clean)
			$db->query("DELETE FROM titles_map WHERE page_id = $page_id");
		
		foreach($words as $word)
		{
			if(!$word)
				continue;
				
			$word = $Stemmer->stem_word($word);
			
			if(strlen($word) > 16)
				$word = substr($word, 0, 16);
				
			$word_id = $db->get("SELECT id FROM words WHERE word = '".addslashes($word)."'");
			if(!$word_id)
			{
				$db->insert('words', array('word' => $word));
				$word_id = $db->last_id();
			}
			
			$db->insert_ignore("titles_map", array('word_id'=>$word_id, 'page_id'=>$page_id));
		}
	}

	function index_split($text)
	{
//		return preg_split("!\s+|\.(\s+|$)|,\s*|:|;|\.\.\.|\(|\)|\"|\{|\}|\[|\]!", $text);
		return preg_split("![^\wА-ЯЁа-яё\-\.]|[\._\-]+(\s+|$)!u", $text);
	}

	function find_in_topics($query)
	{
		// +word -word word
		
		$words = preg_split("!\s+!u", trim($query));
		
		if(!$words)
			return array();

		include_once("include/classes/text/Stem_ru.php");
			
		$db = new DataBase('AB_FORUMS');

		$Stemmer = new Lingua_Stem_Ru();
				
		$must = array();
		$none = array();
		$maybe= array();
		foreach($words as $word)
//			if($word{0} == '+')
//				$must[] = get_word_id(substr($word, 1));
			if($word{0} == '-')
				$none[] = get_word_id(substr($word, 1));
			else
				$maybe[] = get_word_id($word);
	
		$cross = array();
		if($maybe)
		{
			$first = true;
			foreach($maybe as $w)
			{
				$res = $db->get_array("SELECT DISTINCT g.local_id 
					FROM SEARCH.titles_map t
						INNER JOIN HTS.global_ids g ON g.id = t.page_id
					WHERE t.word_id = $w");
				if($first)
					$cross = $res;
				else
					$cross = array_intersect($cross, $res);

				$first = false;
			}
		}

		if($none)
			$corss = array_diff($cross, $db->get_array("SELECT DISTINCT g.local_id 
				FROM SEARCH.titles_map t
					INNER JOIN HTS.global_ids g ON g.id = t.page_id
				WHERE t.word_id IN (".join(",", $none).")"));
		return $cross;
	}

	function get_word_id($word, $db = NULL)
	{
		include_once("include/classes/text/Stem_ru.php");
		
		$word = trim($word);

		if(!$word)
			return 0;
			
		if(!$db)
			$db = new DataBase('SEARCH');

		$Stemmer = new Lingua_Stem_Ru();
		$word = $Stemmer->stem_word($word);
			
		if(strlen($word) > 16)
			$word = substr($word, 0, 16);
			
		$word_id = $db->get("SELECT id FROM words WHERE word = '".addslashes($word)."'");
		if(!$word_id)
		{
			$db->insert('words', array('word' => $word));
			$word_id = $db->last_id();
		}
		
		return $word_id;
	}

    function search_titles_like($title, $limit=20, $forum=0)
    {
		$words = preg_split("!\s+!u", trim($query));
		
		if(!$words)
			return array();

		include_once("include/classes/text/Stem_ru.php");
			
		$db = new DataBase('AB_FORUMS');

		$Stemmer = new Lingua_Stem_Ru();
				
		$search = array();
		foreach($words as $word)
			$search[] = get_word_id($word);
	
		$cross = array();
		if($maybe)
		{
			$first = true;
			foreach($maybe as $w)
			{
				$res = $db->get_array("SELECT DISTINCT g.local_id 
					FROM SEARCH.titles_map t
						INNER JOIN HTS.global_ids g ON g.id = t.page_id
					WHERE t.word_id = $w");
				if($first)
					$cross = $res;
				else
					$cross = array_intersect($cross, $res);

				$first = false;
			}
		}


        $weights = array();

        if($forum == 0 && !empty($GLOBALS['cur_topic']))
        	$forum = $GLOBALS['cur_topic']['forum_id'];

        foreach($title as $word)
        {
            if(strlen($word) <= 2)
				continue;

	        $chkw = new Cache();
        	if($chkw->get("forum_titles_with_key-$ver", $word))
            	$topics = unserialize($chkw->last);
			else
			{
				$topics      = $db->get_array("SELECT id, `num_replies`, `subject`, `forum_id`, 3 as weight FROM topics WHERE subject   LIKE '% ".addslashes($word)." %'  AND `moved_to` IS NULL AND forum_id != 37 ORDER BY `num_replies` DESC LIMIT ".intval($limit));
	            if(strlen($word) > 2)
					$topics += $db->get_array("SELECT id, `num_replies`, `subject`, `forum_id`, 2 as weight FROM topics WHERE `subject` LIKE '% ".addslashes($word)."%'   AND `moved_to` IS NULL AND forum_id != 37 ORDER BY `num_replies` DESC LIMIT ".intval($limit));
	            if(strlen($word) > 4)
					$topics += $db->get_array("SELECT id, `num_replies`, `subject`, `forum_id`, 1 as weight FROM topics WHERE `subject` LIKE '%".addslashes($word)."%'    AND `moved_to` IS NULL AND forum_id != 37 ORDER BY `num_replies` DESC LIMIT ".intval($limit));

	        	$chkw->set(serialize($topics));
		 	}

		 	$n=1;
			foreach($topics as $t)
			{
			    if(empty($weights[$t['id']]))
			    	$weights[$t['id']] = 0;

			    $w = $t['weight'] * log($t['num_replies']+1) / ($n++ + sqrt(sizeof($topics))) + 1;
			    if($forum == $t['forum_id'])
				    $w *= 2;

				$weights[$t['id']] += intval($w*1000);
				$topics_info[$t['id']] = $t;
			}
		}

		arsort($weights);

        if(!empty($weights) && !empty($topics))
        {
//            $out .= "<dl class=\"box\"><dt>Похожие темы форума</dt>\n<dd>\n";
//            $out .= "<b>Похожие заголовки форума</b><br />\n";
            $n = 0;
            foreach($weights as $tid => $w)
            {
            	if($n<$limit)
				{
	                $t = $topics_info[$tid];
	                if(!preg_match("!^From:!", $t['subject']))
	                {
	                	$n++;
						$sub = $t['id'] % 1000;
	    	            $out .= "<a href=\"http://www.balancer.ru/forum/topic/$sub/{$t['id']}/\" title=\"[{$w}]\"><img src=\"http://www.airbase.ru/img/design/icons/topic-9x10.png\" width=\"9\" heght=\"10\" border=\"0\" align=\"absmiddle\">&nbsp;{$t['subject']}</a><br />\n";//&nbsp;&#183;&nbsp;
	    	       	}
				}
            }
//            $out .= "</dd></dl>\n";
        }

        return $ch->set($out, 86400+rand(0,86400));
    }

	function find_in_posts($query)
	{
		// +word -word word
		
		$words = preg_split("!\s+!u", trim($query));
		
		if(!$words)
			return array();

		include_once("include/classes/text/Stem_ru.php");
			
		$db = new DataBase('AB_FORUMS');

		$Stemmer = new Lingua_Stem_Ru();
				
		$must = array();
		$none = array();
		$maybe= array();
		foreach($words as $word)
//			if($word{0} == '+')
//				$must[] = get_word_id(substr($word, 1));
			if($word{0} == '-')
				$none[] = get_word_id(substr($word, 1));
			else
				$maybe[] = get_word_id($word);
	
		$cross = array();
		if($maybe)
		{
			$first = true;
			foreach($maybe as $w)
			{
				$res = $db->get_array("SELECT DISTINCT p.topic_id 
					FROM SEARCH.body_".substr($w,-1)." b
						INNER JOIN punbb.posts p ON p.id = b.page_id
					WHERE b.word_id = $w");
				if($first)
					$cross = $res;
				else
					$cross = array_intersect($cross, $res);

				$first = false;
			}
		}

		if($none)
			$corss = array_diff($cross, $db->get_array("SELECT DISTINCT p.topic_id 
				FROM SEARCH.body_".substr($w,-1)." b
					INNER JOIN punbb.posts p ON p.id = b.page_id
				WHERE b.word_id IN (".join(",", $none).")"));
		return $cross;
	}
