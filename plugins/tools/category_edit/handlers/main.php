<?
	register_action('category_add', 'plugins_tools_categories_editor_category_add');

	hts_data_prehandler("()(.*)/?", array(
			'body' => 'plugins_tools_categories_editor_body',
			'title' => 'plugins_tools_categories_editor_title',
			'parent' => 'plugins_tools_categories_editor_parent',
		));

	function plugins_tools_categories_editor_body($uri, $match, $plugin_data)
	{
		include_once("funcs/datetime.php");
		
		$category = "category://{$_SERVER['HTTP_HOST']}/".@$match[2];
	
		$data = array();
		
		$data['base_uri'] = $plugin_data['base_uri'];
		
		$hts = &new DataBaseHTS;
	
        include_once("funcs/templates/assign.php");

		$data['category'] = $category;

		$categories_list = array();		
		foreach($hts->get_data_array($category, 'child') as $cat)
			$categories_list[] = array(
				'title'		=> $hts->get_data($cat, 'title'), 
				'link'		=> $plugin_data['base_uri'].preg_replace('!category://[^/]+/!', '', $cat),
				'default'	=> $hts->is_flag($cat, 'default'),
				'order'		=> $hts->get_data($cat, 'order'),
			);
		
		usort($categories_list, create_function('$a, $b', 'return $a["order"] != $b["order"] ? $a["order"] > $b["order"] : $a["title"] > $b["title"];'));
		
		$data['categories_list'] = $categories_list;

		include_once("data/arrays.php");
		
		foreach($GLOBALS['icons']['tools'] as $key => $value)
			$data['icons']['tools'][$key] = $value;
		
//		echo "<xmp>"; print_r($data); echo "</xmp>";
		
        return template_assign_data("main.html", $data);
	}

	function plugins_tools_categories_editor_parent($uri, $match)
	{
//		include_once('funcs/DataBaseHTS.php');
		
//		$category = "category://{$_SERVER['HTTP_HOST']}/".@$match[2];
//		$hts = new DataBaseHTS;
//		$parent = $hts->get_data($category, 'parent');
			
		return array(preg_replace("!^(.+?)[^/]+/$!", "$1", $uri));
	}

    function plugins_tools_categories_editor_category_add($uri, $action, $match, $plugin_data)
	{
		include_once('funcs/DataBaseHTS.php');
		include_once('funcs/templates/smarty.php');
		require_once('funcs/system.php');
		require_once('funcs/modules/messages.php');

		$hts = &new DataBaseHTS;
		$us = &new User;

		if(!$us->data('id') || !$us->data('name'))
			return error_message(ec("Вы не вошли в систему"));

		if($us->data('level') < 3)
			return error_message(ec("Недостаточный уровень доступа (нужен 3)"));

		if(!isset($_POST['title']) || !isset($_POST['title_latin']))
			return error_message(ec("Не указано название категории"));

		$category = "category://{$_SERVER['HTTP_HOST']}/".str_replace($plugin_data['base_uri'], "", $uri);
		$new      = $category.$_POST['title_latin']."/";
		
		$hts->set_data($new, 'title', $_POST['title']);
		if(!empty($_POST['order']))
		$hts->set_data($new, 'order', $_POST['order']);
		$hts->nav_link($category, $new);

		go($uri);
		return true;
	}
	
	function plugins_tools_categories_editor_title($uri, $match, $plugin_data)
	{
		$cat = "category://{$_SERVER['HTTP_HOST']}/".str_replace("{$plugin_data['base_uri']}", "", $uri);
		$hts = &new DataBaseHTS;
		$title = $hts->get_data($cat, 'title');
		if(!$title || $uri == $plugin_data['base_uri'])
			$title = ec('Редактор категорий');

		return $title;
	}
