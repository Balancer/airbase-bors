<?
	function modules_design_navleft_get($uri)
	{
//		echo "uri=$uri";

//		DebugBreak();
	
		include_once("funcs/Cache.php");
		$ch = &new Cache();
		
//		if($ch->get('modules-design-navleft-4', $uri))
//			return $ch->last;
	
		include_once("funcs/templates/assign.php");

		$hts = &new DataBaseHTS();

//		$children = $hts->get_data_array($uri, 'child');
		$children = $hts->get_children_array_ex($uri, array('order' => 'order asc'));

		$data = array();
	
//		Явные дети нашей страницы
		foreach($children as $child)
			if($hts->get_data($child, 'nav_name'))
				$data[$child] = array(
					'uri' => $child,
					'title' => $hts->get_data($child, 'nav_name'),
					'children' => $hts->get_data_array_size($child, 'child'),
					'indent' => 0,
				);

		$data = modules_design_navleft_get_parent($uri, $data, 1);

//		echo "<xmp>"; print_r($data); echo "</xmp>";

		$max = 0;

		foreach($data as $d)
			if($d['indent'] > $max)
				$max = $d['indent'];

		foreach($data as $d)
		{
			$d['indent'] = $max - $d['indent'];
			$data2[] = $d;
		}
		
		return $ch->set(template_assign_data("navleft.htm", array('links'=>$data2)), 86400*7);
	}
	

	function modules_design_navleft_get_parent($uri, $children, $indent)
	{
//		echo "<span style=\"font-size: 6pt;\">$indent: $uri</span><br/>\n";

		if($indent > 10)
			return $children;

		$list = array();
		
		$hts = &new DataBaseHTS();

		$we = array();
		$we[$uri] = modules_design_navleft_fill($uri, $indent);
		if($children)
			$we = array_merge($we, $children);

//		echo "<xmp>"; print_r($we); echo "</xmp>";

//		-----------------------------------
//		Собираем информацию о братьях:
//		дети первого родителя - наши братья
//		-----------------------------------

		$parents = $hts->get_data_array($uri, 'parent');

		foreach($parents as $parent)
		{
//			echo "<span style=\"font-size: 6pt;\">$indent: $parent</span><br/>\n";
			if($parent == $uri)
				continue;

			$children_list = array();

// 			Цикл по нашим братьям
			foreach($hts->get_children_array_ex($parent, array('order' => 'order asc')) as $child)
			{
//				echo "<span style=\"font-size: 6pt;\">-- $indent: $child</span><br/>\n";
				if(!$hts->get_data($child, 'nav_name'))
					continue;

				$children_list[$child] = modules_design_navleft_fill($child, $indent);
				
				if($child == $uri && $we)
				{
					// Если это наша страница - добавляем подготовленный заранее блок детей.
					$children_list = array_merge($children_list, $we);
					$we = false;
				}
			}

			$list = array_merge($list, modules_design_navleft_get_parent($parent, $children_list, $indent + 1));
		}

		return $we ? $we : $list;
	}

	function modules_design_navleft_fill($uri, $indent)
	{
		global $hts;
	
		return array(
				'uri' => $uri,
				'title' => $hts->get_data($uri, 'nav_name'),
				'indent' => $indent,
				'children' => $hts->get_data_array_size($uri, 'child'),
		);
	}
