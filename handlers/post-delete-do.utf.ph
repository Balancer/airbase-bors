<?
    register_action_handler('post-delete-do', 'handler_action_post_delete_do');

    function handler_action_post_delete_do($uri, $action)
	{
		include_once("funcs/datetime.php");
	
		$us = new User;

		if(!$us->data('id'))
		{
			$GLOBALS['page_data']['title'] = "Ошибка";
			$GLOBALS['page_data']['source'] = 'Вы не зашли в систему.';

			show_page($uri);
			return true;
		}

		if(!access_allowed($uri))
		{
			$GLOBALS['page_data']['title'] = "Ошибка";
			$GLOBALS['page_data']['source'] = 'У Вас недостаточно прав для выполнения операции';

			show_page($uri);
			return true;
		}

		$hts = new DataBaseHTS();
		$parent = $hts->get_data('parent', $uri);
		
//		$hts->delete_page($uri);
		$hts->set_data('source', $uri, strftime("%d.%m.%Y %H:%M").": Сообщение удалено модератором конференции");

		go($parent ? $parent : "http://{$GLOBALS['cms']['conferences_host']}/{$GLOBALS['cms']['conferences_path']}");

		return true;
	}
?>
