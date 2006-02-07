<?
	function modules_design_navleft_get($uri)
	{
		include_once("funcs/templates/assign.php");
		return template_assign_data("xfile:".dirname(__FILE__)."/navleft.htm", array('links'=>modules_design_navleft($uri, NULL, 0)));
	}
	
	function modules_design_navleft($uri, $data, $indent)
	{
		$hts = new DataBaseHTS();
		
		if(!$data)
		{
			$data[] = array(
					'indent'=>$indent,
					'uri'=>'',
					'title'=>$hts->get_data($uri, 'nav_name'),
					'children'=>sizeof($hts->get_data_array($uri, 'child'))
				);
			$GLOBALS['navleft-parent-seen'] = array();
			if($hts->get_data_array($uri, 'child'))
			{
				foreach($hts->dbh->get_array("
					SELECT ch.value as uri,
							nn.value as title,
							".intval($indent+1)." as indent
						FROM hts_data_child ch
							LEFT JOIN hts_data_nav_name nn ON nn.id = ch.value
						WHERE ch.id = '".addslashes($uri)."'
							AND nn.id IS NOT NULL
						GROUP BY ch.value
						ORDER BY nn.value") as $u)
					$data[] = $u + array('children'=>sizeof($hts->get_data_array($u['uri'],'child')));
			}
		}
					
		$parent = $hts->get_data($uri, 'parent');
		
		if(!$parent || !empty($GLOBALS['navleft-parent-seen'][$parent]))
			return $data;

		$out = array();
		if($hts->get_data_array($parent, 'parent'))
			$out = array(
					'indent'=>$indent, 
					'uri' => $parent, 
					'title'=>$hts->get_data($parent, 'nav_name'),
					'children'=>sizeof($hts->get_data_array($parent, 'child'))
				);

		$indent++;		
			
		$GLOBALS['navleft-parent-seen'][$parent] = true;
		
		foreach($hts->dbh->get_array("
			SELECT ch.value as uri,
					nn.value as title,
					".intval($indent)." as indent
				FROM hts_data_child ch
					LEFT JOIN hts_data_nav_name nn ON nn.id = ch.value
				WHERE ch.id = '".addslashes($parent)."'
					AND nn.id IS NOT NULL
				GROUP BY ch.value
				ORDER BY nn.value
			") as $u)
		{
			if($u['uri'] == $uri)
				$out += $data;
			else
				$out[] = $u + array('children'=>sizeof($hts->get_data_array($u['uri'],'child')));
		}
		return modules_design_navleft($parent, $out, $indent);
	}
?>