<?
    register_action('delete-do', 'handler_action_delete_do');

    function handler_action_delete_do($uri, $action)
	{
		include_once("funcs/datetime.php");
	
		$us = new User;

		if(!$us->data('id'))
		{
			$GLOBALS['page_data']['title'] = ec("Ошибка");
			$GLOBALS['page_data']['source'] = ec('Вы не зашли в систему.');

			show_page($uri);
			return true;
		}

		if(!access_allowed($uri))
		{
			$GLOBALS['page_data']['title'] = ec("Ошибка");
			$GLOBALS['page_data']['source'] = ec('У Вас недостаточно прав для выполнения операции');

			show_page($uri);
			return true;
		}

		if(user_data('level') < 10)
		{
			$GLOBALS['page_data']['source'] = ec('У Вас недостаточно прав для выполнения операции');
			show_page($uri);
			return true;
		}

		$hts = new DataBaseHTS();
		$parent = $hts->get_data_array($uri, 'parent');
		$parent = $parent[0];
		
		$hts->delete_page($uri);

		go($parent ? $parent : "http://{$GLOBALS['cms']['conferences_host']}/{$GLOBALS['cms']['conferences_path']}/");

		return true;
	}
?>
