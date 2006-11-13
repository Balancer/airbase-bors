<?
	function modules_design_navleft_get($uri)
	{
//		echo "uri=$uri";

//		DebugBreak();
	
		include_once("funcs/Cache.php");
		$ch = &new Cache();
		
		if($ch->get('modules-design-navleft-4', $uri))
			return $ch->last;
	
		include_once("funcs/templates/assign.php");

		$hts = &new DataBaseHTS();

//		$children = $hts->get_data_array($uri, 'child');
		$children = $hts->get_children_array_ex($uri, array('order' => 'order asc'));

		$data = array();
	
//		Это статус нашей собственной страницы
		$data[$uri] = array(
			'indent' => 1,
			'uri' => $uri,
			'title' => $hts->get_data($uri, 'nav_name'),
			'children' => sizeof($children),
			'here' => true,
		);


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
	

	function modules_design_navleft_get_parent($uri, &$children, $indent)
	{
//		echo "<span style=\"font-size: 6pt;\">$indent: $uri</span><br/>\n";

		if($indent > 20)
			return $children;

		$list = array();
		
		$hts = &new DataBaseHTS();

//		-----------------------------------
//		Собираем информацию о братьях:
//		дети первого родителя - наши братья
//		-----------------------------------

		$parents = $hts->get_data_array($uri, 'parent');

		// Список реально использовавшихся родителей
		$parents2 = array();
		
		foreach($parents as $parent)
		{
//			if(!empty($children[$parent]) || !empty($list[$parent]) || $parent == $uri)
			if($parent == $uri)
				continue;

			$parents2[] = $parent;

//			echo "<span style=\"font-size: 6pt;\">&nbsp;p: $parent</span><br/>\n";

// 			Цикл по нашим братьям
			foreach($hts->get_children_array_ex($parent, array('order' => 'order asc')) as $child)
			{
//				echo "<span style=\"font-size: 6pt;\">&nbsp;&nbsp;c: $child</span><br/>\n";
				if(!$hts->get_data($child, 'nav_name'))
					continue;

				$list[$child] = array(
					'uri' => $child,
					'title' => $hts->get_data($child, 'nav_name'),
					'indent' => $indent,
					'children' => $hts->get_data_array_size($child, 'child'),
				);
				
				if($child == $uri && $children)
				{
//					echo "<span style=\"font-size: 6pt;\">----- join </span><br/>\n";
					$list = array_merge($list, $children);
					$children = false;
				}
			}
		}

		if($children !== false)
			$children = array_merge($children, $list);
		else
			$children = $list;

		$list = array();
		foreach($parents2 as $parent)
			if(empty($children[$parent]) && empty($list[$parent]))
				$list = array_merge($list, modules_design_navleft_get_parent($parent, $children, $indent + 1));
	
		if($children !== false)
			$children = array_merge($children, $list);
		else
			$children = $list;
			
		return $children;
	}
