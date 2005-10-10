<?
    register_action_handler('add-favorite', 'handler_add_favorite');

    function handler_add_favorite($uri, $action)
	{
		$ht = new DataBaseHTS;

		$us = new User;

		$uid = $us->data('id');

		if(!$uid)
		{
			$GLOBALS['page_data']['title'] = "Ошибка";
			$GLOBALS['page_data']['source'] = 'Вы не зашли в систему.';

			show_page($uri);
			return true;
		}


		$favor = "http://{$GLOBALS['cms']['conferences_host']}/{$GLOBALS['cms']['conferences_path']}/favorites/user$uid/";

//		$GLOBALS['log_level'] = 9;
		$ht->add_child($favor, $uri);

		include_once("funcs/actions/subscribe.php");
		cms_funcs_action_subscribe($uri);

		go($uri);
		return true;
	}
?>
