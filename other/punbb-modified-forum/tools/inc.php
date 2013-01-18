<?php

	function punbb_get_all_subforums($forum_id)
	{
		static $loaded = array();

		$forum_id = intval($forum_id);
		if(!empty($loaded[$forum_id]))
			return $loaded[$forum_id];

		$db = new driver_mysql(config('punbb.database', 'AB_FORUMS'));

		forum_forum::all_forums_preload(true);
		$forum = object_load('forum_forum', $forum_id);

		$subforum_ids = $db->select_array('forums', 'id', array("tree_map LIKE '{$forum->tree_map()}{$forum_id}.%'"));
//		if(debug_is_balancer())			print_d($subforum_ids);
		return $subforum_ids;
	}

	function punbb_get_all_subcategories($cat_id)
	{
		$cat_id = intval($cat_id);

		$cids    = array();
		$checked = array();

		$cids[] = $cat_id;

		$db = new driver_mysql(config('punbb.database', 'AB_FORUMS'));

		do
		{
			$append = false;
			foreach($cids as $cid)
				if(!in_array($cid, $checked))
				{
					$cids2 = $cids;
					$checked[] = $cid;
					foreach($db->get_array("SELECT id FROM categories WHERE parent = $cid ORDER BY disp_position") as $id)
						if(!in_array($id, $cids2))
							$cids2[] = $id;
					$cids = $cids2;
					$append = true;
				}
		} while($append);
		
		array_shift($cids);
		return $cids;
	}

	function make_js($txt)
	{
		$out = "with(document){\n";

	    foreach(explode("\n", $txt) as $s)
    	{
	        $s=str_replace("\\","\\\\",$s);
    	    $s=str_replace("\"","\\\"",$s);
	        $s=str_replace("\n"," ",$s);
    	    $s=str_replace("\r"," ",$s);
        	$s=preg_replace("! src=(\")?/!", " src=$1http://www.airbase.ru/", $s);
	        $s=preg_replace("! href=(\")?/!", " href=$1http://www.airbase.ru/", $s);
    	    $out .= "write(\"$s\");\n";
	    }
		
		$out .= "}\n";
		
		return $out;
	}
