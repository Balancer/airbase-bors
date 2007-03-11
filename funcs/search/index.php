<?
	function index_body($page_id, $text)
	{
		$text = trim($text);
		$page_id = intval($page_id);
		
		if(!$text || !$page_id)
			return;

		include_once("include/classes/text/Stem_ru.php");
			
		$db = &new DataBase('SEARCH');

		$Stemmer = &new Lingua_Stem_Ru();
				
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
			
			$count = intval($db->get("SELECT count FROM body_$sub WHERE id = $id AND page_id = $page_id"));
			if(!$count)
				$db->replace("body_$sub", array('id'=>$id, 'page_id'=>$page_id, 'count'=>1));
			else
				$db->update("body_$sub", "id = $id AND page_id = $page_id", array('count' => $count+1));
		}
	}

	function index_title($page_id, $text, $clean=true)
	{
		$text = trim($text);
		$page_id = intval($page_id);
		
		if(!$text || !$page_id)
			return;

		include_once("include/classes/text/Stem_ru.php");
			
		$db = &new DataBase('SEARCH');

		$Stemmer = &new Lingua_Stem_Ru();
				
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
			
		$db = &new DataBase('punbb');

		$Stemmer = &new Lingua_Stem_Ru();
				
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
			$db = &new DataBase('SEARCH');

		$Stemmer = &new Lingua_Stem_Ru();
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
