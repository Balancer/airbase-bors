<?
	echo modules_design_navleft($GLOBALS['main_uri']);

	function modules_design_navleft($uri, $data = NULL)
	{
		$hts = new DataBaseHTS();
		
		if(!$data)
		{
			$data = "<li><b>".$hts->get_data($uri, 'nav_name')."</b></li>\n";
			$GLOBALS['navleft-parent-seen'] = array();
			if($hts->get_data_array($uri, 'child'))
			{
				$data .= "<ul class=\"navleft\">\n";
				foreach($hts->dbh->get_array("
					SELECT ch.value 
						FROM hts_data_child ch
							LEFT JOIN hts_data_nav_name nn ON (nn.id = ch.value)
						WHERE ch.id = '".addslashes($uri)."'
							AND nn.id IS NOT NULL
						GROUP BY ch.value
						ORDER BY nn.value") as $u)
				{
					$data .= "<li><a href=\"$u\">".$hts->get_data($u, 'nav_name')."</a></li>\n";
				}
				$data .= "</ul>\n";
			}
		}
					
		$parent = $hts->get_data($uri, 'parent');
		
		if(!$parent || !empty($GLOBALS['navleft-parent-seen'][$parent]))
			return "<ul class=\"navleft\">\n$data\n</ul>\n";

		$out = "";
		if($hts->get_data_array($parent, 'parent'))
			$out = "<li><a href=\"$parent\"><b>".$hts->get_data($parent, 'nav_name')."</b></a></li>\n";
		$out .= "<ul class=\"navleft\">\n";
		
			
		$GLOBALS['navleft-parent-seen'][$parent] = true;
		
		foreach($hts->dbh->get_array("
			SELECT ch.value 
				FROM hts_data_child ch
					LEFT JOIN hts_data_nav_name nn ON (nn.id = ch.value)
				WHERE ch.id = '".addslashes($parent)."'
					AND nn.id IS NOT NULL
				GROUP BY ch.value
				ORDER BY nn.value
			") as $u)
		{
			if($u == $uri)
				$out .= $data;
			else
				$out .= "<li><a href=\"$u\">".$hts->get_data($u, 'nav_name')."</a></li>\n";
		}

		$out .= "</ul>\n";

		return modules_design_navleft($parent, $out);
	}
?>