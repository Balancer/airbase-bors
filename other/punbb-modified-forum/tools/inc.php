<?
	function punbb_get_all_subforums($forum_id)
	{
		$forum_id = intval($forum_id);

		$fids    = array();
		$checked = array();
		
		$fids[] = $forum_id;
				
		include("db_config.php");
		$db = new DataBase();
		do
		{
			$append = false;
			foreach($fids as $fid)
				if(!in_array($fid, $checked))
				{
					$fids2 = $fids;
					$checked[] = $fid;
					foreach($db->get_array("SELECT id FROM forums WHERE parent = $fid ORDER BY disp_position") as $id)
						if(!in_array($id, $fids2))
							$fids2[] = $id;
					$fids = $fids2;
					$append = true;
				}
		} while($append);
		
		array_shift($fids);
		return $fids;
	}

	function punbb_get_all_subcategories($cat_id)
	{
		$cat_id = intval($cat_id);

		$cids    = array();
		$checked = array();
		
		$cids[] = $cat_id;
				
		include("db_config.php");
		$db = new DataBase();
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

	    foreach(split("\n", $txt) as $s)
    	{
	        $s=str_replace("\\","\\\\",$s);
    	    $s=str_replace("\"","\\\"",$s);
	        $s=str_replace("\n"," ",$s);
    	    $s=str_replace("\r"," ",$s);
        	$s=preg_replace("! src=(\")?/!", " src=$1http://airbase.ru/", $s);
	        $s=preg_replace("! href=(\")?/!", " href=$1http://airbase.ru/", $s);
    	    $out .= "write(\"$s\");\n";
	    }
		
		$out .= "}\n";
		
		return $out;
	}
