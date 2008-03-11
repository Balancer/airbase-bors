<?
	function modules_design_navleft_get($uri)
	{
		require_once("engines/bors.php");
		$ch = &new Cache();
		
		if($ch->get('modules-design-navleft-v9', $uri))
			return $ch->last();
	
		include_once("funcs/templates/assign.php");

		$hts = &new DataBaseHTS();

//		$children = $hts->get_data_array($uri, 'child');
		$children = $hts->get_children_array_ex($uri, array('order' => 'order asc', 'range' => -1));

		$data = array();
	
//		Явные дети нашей страницы
		if(!isset($GLOBALS['module_data']['downlevel']) || $GLOBALS['module_data']['downlevel'] > 0)
			foreach($children as $child)
				if($hts->get_data($child, 'nav_name'))
					$data[$child] = array(
						'uri' => bors()->real_uri($child),
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

		$data2 = array();

		foreach($data as $d)
		{
			$d['indent'] = $max - $d['indent'];
			if(is_object($d['uri']))
				$d['uri'] = $d['uri']->url();
			$data2[] = $d;
		}
		
		$tpl = "navleft.htm";
		if(!empty($GLOBALS['module_data']['template']))
			$tpl = $GLOBALS['module_data']['template'];

		unset($GLOBALS['module_data']);
		return $ch->set(template_assign_data($tpl, array('links'=>$data2)), 86400*7);
	}
	

	function modules_design_navleft_get_parent($uri, $children, $indent)
	{
//		echo "<span style=\"font-size: 6pt;\">$indent: $uri</span><br/>\n";

		if($indent > 10)
			return $children;

		$list = array();
		
		$hts = &new DataBaseHTS();

		$we = array();

		if(empty($GLOBALS['module_data']['self_hide']))
			$we[$uri] = modules_design_navleft_fill($uri, $indent);
	
		if($children)
			$we = array_merge($we, $children);

//		echo "<xmp>"; print_r($we); echo "</xmp>";

//		-----------------------------------
//		Собираем информацию о братьях:
//		дети первого родителя - наши братья
//		-----------------------------------

		if(!isset($GLOBALS['module_data']['uplevel']) || (isset($GLOBALS['module_data']['uplevel']) && $GLOBALS['module_data']['uplevel'] + 1 > $indent))
			$parents = $hts->get_data_array($uri, 'parent');
		else
			$parents = array();

		foreach($parents as $parent)
		{
//			echo "<span style=\"font-size: 6pt;\">$indent: $parent</span><br/>\n";
			if($parent == $uri)
				continue;

			$children_list = array();

// 			Цикл по нашим братьям
			foreach($hts->get_children_array_ex($parent, array('order' => 'order asc', 'range' => -1)) as $brother)
			{
//				echo "<span style=\"font-size: 6pt;\">-- $indent: $child</span><br/>\n";
				if(!$hts->get_data($brother, 'nav_name'))
					continue;

				if(empty($GLOBALS['module_data']['self_hide']) || $brother != $uri)
					$children_list[$brother] = modules_design_navleft_fill($brother, $indent);
				
				if($brother == $uri && $we)
				{
					// Если это наша страница - добавляем подготовленный заранее блок детей.
					$children_list = array_merge($children_list, $we);
					$we = false;
				}
			}

//			if($we)
//			{
//				$list = array_merge($list, $we);
//				$we = false;
//			}

//			echo "<xmp>"; print_r($list); echo "</xmp>";
			$list = array_merge($list, modules_design_navleft_get_parent($parent, $children_list, $indent + 1));
		}

//		echo "<xmp>"; print_r($list); echo "</xmp>";
		return $we ? $we : $list;
		return $we ? array_merge($list, $we) : $list;
	}

	function modules_design_navleft_fill($uri, $indent)
	{
		require_once("engines/bors.php");
		$hts = &new DataBaseHTS();
	
		return array(
				'uri' => bors()->real_uri($uri),
				'title' => $hts->get_data($uri, 'nav_name'),
				'indent' => $indent,
				'children' => $hts->get_data_array_size($uri, 'child'),
		);
	}
