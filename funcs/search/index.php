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
		
		for($i=0; $i<100; $i++)
			$db->query("DELETE FROM body_".sprintf("%02d", $i)." WHERE page_id = $page_id");
		
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

			$sub = sprintf("%02d", $id%100);
			
			$count = intval($db->get("SELECT count FROM body_$sub WHERE id = $id AND page_id = $page_id"));
			if(!$count)
				$db->replace("body_$sub", array('id'=>$id, 'page_id'=>$page_id, 'count'=>1));
			else
				$db->update("body_$sub", "id = $id AND page_id = $page_id", array('count' => $count+1));
		}
	}

	function index_title($page_id, $text)
	{
		$text = trim($text);
		$page_id = intval($page_id);
		
		if(!$text || !$page_id)
			return;

		include_once("include/classes/text/Stem_ru.php");
			
		$db = &new DataBase('SEARCH');

		$Stemmer = &new Lingua_Stem_Ru();
				
		$words = index_split($text);
		
		for($i=0; $i<100; $i++)
			$db->query("DELETE FROM title_".sprintf("%02d", $i)." WHERE page_id = $page_id");
		
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
			
			$sub = sprintf("%02d", $id % 100);
			$db->replace("title_$sub", array('id'=>$id, 'page_id'=>$page_id));
		}
	}

	function index_split($text)
	{
//		return preg_split("!\s+|\.(\s+|$)|,\s*|:|;|\.\.\.|\(|\)|\"|\{|\}|\[|\]!", $text);
		return preg_split("![^\wА-ЯЁа-яё\-\.]|[\._\-]+(\s+|$)!u", $text);
	}
