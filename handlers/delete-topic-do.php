<?
    register_action_handler('delete-topic-do', 'handler_delete_topic_do');

    function handler_delete_topic_do($uri, $action)
	{
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
		
		$hts->delete_page($uri);
		go($parent);

		return true;
	}
?>
