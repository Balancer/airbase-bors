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
//		echo "Get for ".print_r($uri, true)."<br/>";
	
		$GLOBALS['loop']++;
		
		if($GLOBALS['loop'] > 50)
			return $data;
		
//		if(!empty($data[$uri]))
//			return $data;
		
		$hts = new DataBaseHTS();

		$children = $hts->get_data_array($uri, 'child');
		
		$out = array();
		$out["$uri"] = array(
				'indent' => $indent,
				'uri' => $uri,
				'title' => $hts->get_data($uri, 'nav_name'),
				'children' => sizeof($children)
			);

//		echo "Get for $uri:".print_r($out, true)."<br/>";

		if(!$data)
		{
			if($children)
			{
				foreach($children as $child)
				{
					$data[$child] = array(
							'uri' => $child,
							'title' => $hts->get_data($child, 'nav_name'),
							'children' => sizeof($hts->get_data_array($child,'child')),
							'indent' => $indent-1
						);
				}
			}
		}

		$data = array_merge($out, $data);

		$parent = $hts->get_data_array($uri, 'parent');

//		echo "<p><b>Parent for $uri = $parent</b></p>";
//		print_r($data); echo "<br/>";
		
		if(!$parent || !empty($data["$parent"]))
			return $data;

		$parent = $parent[0];

		$indent++;		

		$out = array();
		if($hts->get_data_array($parent, 'parent'))
			$out[$parent] = array(
					'indent' => $indent, 
					'uri' => $parent, 
					'title' => $hts->get_data($parent, 'nav_name'),
					'children' => sizeof($hts->get_data_array($parent, 'child'))
				);

		foreach($hts->get_data_array($parent, 'child') as $child)
		{
			if($child == $uri)
				$out = array_merge($out, $data);
			else
				$out[$child] = array(
						'uri' => $child,
						'title' => $hts->get_data($parent, 'nav_name'),
						'indent' => $indent-1,
						'children' => sizeof($hts->get_data_array($child, 'child')),
					);
		}

		return modules_design_navleft($parent, $out, $indent);
	}
?>
