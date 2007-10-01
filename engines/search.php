<?php

function bors_search_object_index($object)
{
	if(!$object)
		return $object;
	
	$source	= $object->search_source();
	$title	= $object->title();

	include_once("include/classes/text/Stem_ru-{$GLOBALS['cms']['charset_u']}.php");
			
	$db = &new DataBase(config('search_db'));

	$Stemmer = &new Lingua_Stem_Ru();
	
	$class_id	= intval($object->id());
	$class_name	= get_class($object);
	
	if($source)
	{
		$words = index_split($source);
		
		for($i=0; $i<10; $i++)
			$db->query("DELETE FROM bors_search_source_{$i} WHERE class_name = '{$class_name}' AND class_id = {$class_id}");
		
		foreach($words as $word)
		{
			if(!$word)
				continue;
				
			$word = $Stemmer->stem_word($word);
			
			if(strlen($word) > 16)
				$word = substr($word, 0, 16);
				
			$word_id = bors_search_get_word_id($word);

			$sub = $word_id%10;
			
			$count = intval($db->get("SELECT count FROM bors_search_source_{$sub} WHERE word_id = {$word_id} AND class_name = '{$class_name}' AND class_id = {$class_id}"));
			if(!$count)
				$db->replace("bors_search_source_{$sub}", array(
					'word_id' => $word_id, 
					'class_id' => $class_id, 
					'class_name' => $class_name, 
					'count' => 1, 
					'object_create_time' => $object->create_time(), 
					'object_modify_time' => $object->modify_time(),
				));
			else
				$db->update("bors_search_source_{$sub}", "word_id = {$word_id} AND class_name = '{$class_name}' AND class_id = {$class_id}", array(
					'count' => $count+1, 
					'object_create_time' => $object->create_time(), 
					'object_modify_time' => $object->modify_time(),
				));
		}
	}

	if($title)
	{
		$words = index_split($title);
		
		$db->query("DELETE FROM bors_search_titles WHERE class_name = '{$class_name}' AND class_id = {$class_id}");
		
		foreach($words as $word)
		{
			if(!$word)
				continue;
				
			$word = $Stemmer->stem_word($word);
			
			if(strlen($word) > 16)
				$word = substr($word, 0, 16);
				
			$word_id = bors_search_get_word_id($word);
		
			$db->replace("bors_search_titles", array(
				'word_id' => $word_id, 
				'class_id' => $class_id, 
				'class_name' => $class_name, 
				'object_create_time' => $object->create_time(), 
				'object_modify_time' => $object->modify_time(),
			));
		}
	}
}

function index_split($text)
{
	return preg_split('![ -,\./:-@\[-`\{-~\s]+!', $text);
}

function bors_search_in_titles($query)
{
	// +word -word word
		
	$words = preg_split("!\s+!u", trim($query));
		
	if(!$words)
		return array();

	include_once("include/classes/text/Stem_ru-{$GLOBALS['cms']['charset_u']}.php");
			
	$db = &new DataBase(config('search_db'));

	$Stemmer = &new Lingua_Stem_Ru();
				
	$must = array();
	$none = array();
	$maybe= array();

	foreach($words as $word)
	{
//		if($word{0} == '+')
//			$must[] = bors_search_get_word_id(substr($word, 1));
		if($word{0} == '-')
			$none[] = bors_search_get_word_id(substr($word, 1));
		else
			$maybe[] = bors_search_get_word_id($word);
	}
		
	$cross = array();

	if($maybe)
	{
		$first = true;
		foreach($maybe as $w)
		{
			$res = $db->get_array("SELECT DISTINCT class_name, class_id	FROM bors_search_titles WHERE word_id = $w");
			if($first)
				$cross = $res;
			else
				$cross = array_intersect($cross, $res);

			$first = false;
		}
	}

	if($none)
		$cross = array_diff($cross, $db->get_array("SELECT DISTINCT class_name, class_id FROM bors_search_titles WHERE word_id IN (".join(",", $none).")"));

	$result = array();
	
	foreach($cross as $x)
		$result[] = class_load($x['class_name'], $x['class_id']);

	return $result;
}

function bors_search_get_word_id($word, $db = NULL)
{
	include_once("include/classes/text/Stem_ru-{$GLOBALS['cms']['charset_u']}.php");
		
	$word = trim($word);

	if(!$word)
		return 0;
			
	if(!$db)
		$db = &new DataBase(config('search_db'));

	$Stemmer = &new Lingua_Stem_Ru();
	$word = $Stemmer->stem_word($word);
			
	if(strlen($word) > 16)
		$word = substr($word, 0, 16);
			
	$word_id = $db->get("SELECT id FROM bors_search_words WHERE word = '".addslashes($word)."'");

	if(!$word_id)
	{
		$db->insert('bors_search_words', array('word' => $word));
		$word_id = $db->last_id();
	}
		
	return intval($word_id);
}

function search_titles_like($title, $limit=20, $forum=0)
{
	$words = preg_split("!\s+!u", trim($query));
		
	if(!$words)
		return array();

	include_once("include/classes/text/Stem_ru-{$GLOBALS['cms']['charset_u']}.php");
			
	$db = &new DataBase(config('search_db'));

	$Stemmer = &new Lingua_Stem_Ru();
				
	$search = array();
	foreach($words as $word)
		$search[] = bors_search_get_word_id($word);
	
	$cross = array();
	if($maybe)
	{
		$first = true;
		foreach($maybe as $w)
		{
			$res = $db->get_array("SELECT DISTINCT class_name, class_id FROM titles_map WHERE t.word_id = $w");
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
//		$out .= "<dl class=\"box\"><dt>Похожие темы форума</dt>\n<dd>\n";
//		$out .= "<b>Похожие заголовки форума</b><br />\n";
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
					$out .= "<a href=\"http://balancer.ru/forum/topic/$sub/{$t['id']}/\" title=\"[{$w}]\"><img src=\"http://airbase.ru/img/design/icons/topic-9x10.png\" width=\"9\" heght=\"10\" border=\"0\" align=\"absmiddle\">&nbsp;{$t['subject']}</a><br />\n";//&nbsp;&#183;&nbsp;
	    		}
			}
		}
//		$out .= "</dd></dl>\n";
	}

	return $ch->set($out, 86400+rand(0,86400));
}

function bors_search_in_bodies($query)
{
	// +word -word word
		
	$words = preg_split("!\s+!u", trim($query));
		
	if(!$words)
		return array();

	include_once("include/classes/text/Stem_ru-{$GLOBALS['cms']['charset_u']}.php");
			
	$db = &new DataBase(config('search_db'));

	$Stemmer = &new Lingua_Stem_Ru();
				
	$must = array();
	$none = array();
	$maybe= array();

	foreach($words as $word)
	{
//		if($word{0} == '+')
//			$must[] = bors_search_get_word_id(substr($word, 1));
		if(preg_match("!^\-(.+)$!", $word, $m))
			$none[] = bors_search_get_word_id($m[1]);
		else
			$maybe[] = bors_search_get_word_id($word);
	}
	
	$cross = array();
	if($maybe)
	{
		$first = true;
		foreach($maybe as $w)
		{
			$res = $db->get_array("SELECT DISTINCT class_name, class_id FROM bors_search_source_".($w%10)." WHERE word_id = $w");
			if($first)
				$cross = $res;
			else
				$cross = array_intersect($cross, $res);

			$first = false;
		}
	}

	if($none)
		foreach($none as $w)
			$cross = array_diff($cross, $db->get_array("SELECT DISTINCT class_name, class_id FROM bors_search_source_".($w%10)." WHERE word_id = {$w}"));

	$result = array();
	
	foreach($cross as $x)
		$result[] = class_load($x['class_name'], $x['class_id']);

	return $result;
}
