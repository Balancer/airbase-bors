<?
	function modules_design_navleft_get($uri)
	{
		include_once("funcs/templates/assign.php");
		$data = modules_design_navleft($uri, array(), 1);
		$max = 0;

		foreach($data as $d)
			if($d['indent'] > $max)
				$max = $d['indent'];

		foreach($data as $d)
		{
			$d['indent'] = $max - $d['indent'];
			$data2[] = $d;
		}
		
		return template_assign_data("xfile:".dirname(__FILE__)."/navleft.htm", array('links'=>$data2));
	}

	$GLOBALS['loop'] = 0;
	
	function modules_design_navleft($uri, $data, $indent)
	{
		$GLOBALS['loop']++;
		
		if($GLOBALS['loop'] > 50)
			return $data;
		
		if(!empty($data[$uri]))
			return $data;
		
		$hts = new DataBaseHTS();

		$children = $hts->get_data_array($uri, 'child');
		
		$out = array();
		$out['uri'] = array(
				'indent' => $indent,
				'uri' => $uri,
				'title' => $hts->get_data($uri, 'nav_name'),
				'children' => sizeof($children)
			);

		if(!$data)
		{
			if($children)
			{
				foreach($hts->dbh->get_array("
					SELECT ch.value as uri,
							nn.value as title
						FROM hts_data_child ch
							LEFT JOIN hts_data_nav_name nn ON nn.id = ch.value
						WHERE ch.id = '".addslashes($uri)."'
							AND nn.id IS NOT NULL
						GROUP BY ch.value
						ORDER BY nn.value") as $u)
				{
					$data[$u['uri']] = $u + array(
							'children' => sizeof($hts->get_data_array($u['uri'],'child')),
							'indent' => $indent-1
						);
				}
			}
		}

		$data = array_merge($out, $data);

		$parent = $hts->get_data($uri, 'parent');

//		echo "<p><b>Parent for $uri = $parent</b></p>";
//		print_r($data); echo "<br/>";
		
		if(!$parent || !empty($data[$parent]))
			return $data;

		$indent++;		

		$out = array();
		if($hts->get_data_array($parent, 'parent'))
			$out[$parent] = array(
					'indent' => $indent, 
					'uri' => $parent, 
					'title' => $hts->get_data($parent, 'nav_name'),
					'children' => sizeof($hts->get_data_array($parent, 'child'))
				);

		foreach($hts->dbh->get_array("
			SELECT ch.value as uri,
					nn.value as title,
					".intval($indent-1)." as indent
				FROM hts_data_child ch
					LEFT JOIN hts_data_nav_name nn ON nn.id = ch.value
				WHERE ch.id = '".addslashes($parent)."'
					AND nn.id IS NOT NULL
				GROUP BY ch.value
				ORDER BY nn.value
			") as $u)
		{
//			echo "<p>Before:</p><xmp>"; print_r($out); echo "</xmp>;u=".(print_r($u,true))."<br/>";
			{
				if($u['uri'] == $uri)
					$out = array_merge($out, $data);
				else
					$out[$u['uri']] = $u + array('children'=>sizeof($hts->get_data_array($u['uri'],'child')));
			}
//			echo "After:<br/><xmp>"; print_r($out); echo "</xmp><br/>";
		}

		return modules_design_navleft($parent, $out, $indent);
	}
?>