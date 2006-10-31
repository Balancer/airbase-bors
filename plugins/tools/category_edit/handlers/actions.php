<?
    register_action('set-default-flag', 'handler_action_set_default_flag');
    register_action('drop-default-flag', 'handler_action_drop_default_flag');
    register_action('set-order', 'handler_action_set_order');
    register_action('category-delete', 'handler_action_category_delete');

    function handler_action_set_default_flag($uri, $action, $match, $plugin_data)
	{
		include_once("funcs/navigation/go.php");
		$hts = new DataBaseHTS;
		$us = new User;

		$level = $us->data('level');
		
		if($level<3)
			return error_message(ec("У вас недостаточен уровень доступа ($level) для этой операции."));

		$category = "category://{$_SERVER['HTTP_HOST']}/".str_replace("{$plugin_data['base_uri']}", "", $uri);

		$hts->set_flag($category, 'default');
		go_ref();

		return true;
	}

    function handler_action_drop_default_flag($uri, $action, $match, $plugin_data)
	{
		include_once("funcs/navigation/go.php");
		$hts = new DataBaseHTS;
		$us = new User;

		$level = $us->data('level');
		
		if($level<3)
			return error_message(ec("У вас недостаточен уровень доступа ($level) для этой операции."));

		$category = "category://{$_SERVER['HTTP_HOST']}/".str_replace("{$plugin_data['base_uri']}", "", $uri);

		$hts->drop_flag($category, 'default');
		go_ref();

		return true;
	}

    function handler_action_set_order($uri, $action, $match, $plugin_data)
	{
		include_once("funcs/navigation/go.php");
		$hts = new DataBaseHTS;
		$us = new User;

		$level = $us->data('level');
		
		if($level<3)
			return error_message(ec("У вас недостаточен уровень доступа ($level) для этой операции."));

		$category = "category://{$_SERVER['HTTP_HOST']}/".str_replace("{$plugin_data['base_uri']}", "", $uri);

//		exit("Set order of $category = {$_POST['order']}");

		$hts->set_data($category, 'order', @$_POST['order']);
		go_ref();

		return true;
	}

    function handler_action_category_delete($uri, $action, $match, $plugin_data)
	{
		include_once("funcs/navigation/go.php");
		$hts = new DataBaseHTS;
		$us = new User;

		$level = $us->data('level');
		
		if($level<3)
			return error_message(ec("У вас недостаточен уровень доступа ($level) для этой операции."));

		$category = "category://{$_SERVER['HTTP_HOST']}/".str_replace("{$plugin_data['base_uri']}", "", ec(urldecode($uri)));

		$hts->delete_by_mask($category);
		go_ref();

		return true;
	}
