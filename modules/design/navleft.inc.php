<?
	function modules_design_navleft_get($uri)
	{
		include_once("engines/smarty/assign.php");

		$obj = object_load($uri);

		if(!$obj)
			return '';

		$children = $obj->children();

		$data = array();
	
//		Явные дети нашей страницы
		if(!isset($GLOBALS['module_data']['downlevel']) || $GLOBALS['module_data']['downlevel'] > 0)
		{
			if($children)
			{
				foreach($children as $child_url)
				{
					$child = object_load($child_url);
					if(!$child)
						echo "Can't load '$child_url'<br/>";
					if($child && $child->nav_name())
						$data[$child->url()] = array(
							'obj' => $child,
							'children_count' => count($child->children()),
							'indent' => 0,
						);
				}
			}
		}

		$data = modules_design_navleft_get_parent($obj, $data, 1);

//		echo "<xmp>"; print_r($data); echo "</xmp>";

		$max = 0;

		foreach($data as $d)
			if($d['indent'] > $max)
				$max = $d['indent'];

		$data2 = array();

		foreach($data as $d)
		{
			$d['indent'] = $max - $d['indent'];
			$data2[] = $d;
		}
		
		$tpl = "navleft.htm";
		if(!empty($GLOBALS['module_data']['template']))
			$tpl = $GLOBALS['module_data']['template'];

		unset($GLOBALS['module_data']);
		return template_assign_data($tpl, array('links'=>$data2));
	}
	

	function modules_design_navleft_get_parent($obj, $children, $indent)
	{
		if($indent > 10)
			return $children;

		$list = array();
		
		$we = array();

		if(empty($GLOBALS['module_data']['self_hide']))
			$we[$obj->url()] = modules_design_navleft_fill($obj, $indent);
	
		if($children)
			$we = array_merge($we, $children);

//		echo "<xmp>"; print_r($we); echo "</xmp>";

//		-----------------------------------
//		Собираем информацию о братьях:
//		дети первого родителя - наши братья
//		-----------------------------------

		if(!isset($GLOBALS['module_data']['uplevel']) 
				|| (isset($GLOBALS['module_data']['uplevel']) 
					&& $GLOBALS['module_data']['uplevel'] + 1 > $indent))
			$parents = $obj->parents();
		else
			$parents = array();

		foreach($parents as $parent_url)
		{
			$parent = object_load($parent_url);
//			echo "<span style=\"font-size: 6pt;\">$indent: $parent</span><br/>\n";
			if(!$parent || ($parent->url() == $obj->url()))
				continue;

			$children_list = array();

			$parents = $parent->children();
// 			Цикл по нашим братьям
			if($parents)
			{
				foreach($parents as $bro_url)
				{
					$bro = object_load($bro_url);
//					echo "<span style=\"font-size: 6pt;\">-- $indent: $child</span><br/>\n";
					if(!$bro->nav_name())
						continue;

					if(empty($GLOBALS['module_data']['self_hide']) || $brother != $uri)
						$children_list[$bro->url()] = modules_design_navleft_fill($bro, $indent);
					
					if($bro->url() == $obj->url() && $we)
					{
						// Если это наша страница - добавляем подготовленный заранее блок детей.
						$children_list = array_merge($children_list, $we);
						$we = false;
					}
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

function modules_design_navleft_fill($obj, $indent)
{
	return array(
		'obj' => $obj,
		'indent' => $indent,
		'children_count' => count($obj->children()),
	);
}
