<?
    register_action_handler('favorite-remove', 'handler_favorite_remove');

    function handler_favorite_remove($uri, $action)
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

		include_once("funcs/actions/favorites.php");
	
		$favor = cms_funcs_action_favorites_user_page($us);

//		$GLOBALS['log_level'] = 9;
		$ht->remove_nav_link($favor, $uri);

		go("http://{$GLOBALS['cms']['conferences_host']}/{$GLOBALS['cms']['conferences_path']}/users/favorites/");
		return true;
	}
?>
