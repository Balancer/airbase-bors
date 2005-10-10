<?
	register_action_handler('unsubscribe', 'handler_action_unsubscribe');

    function handler_action_unsubscribe($uri, $action)
	{
		$us = new User;

		if($check = $us->check_access($uri))
		{
			$GLOBALS['page_data']['title'] = $check['title'];
			$GLOBALS['page_data']['source'] = $check['title'];

			show_page($uri);
			return true;
		}

		$GLOBALS['page_data']['title'] = "Отписка от темы";
		
		$hts = new DataBaseHTS();

		$message = $hts->get_data($uri, 'source');
		
		$title = $hts->get_data($uri, 'title');

		include_once("funcs/actions/subscribe.php");
		
		cms_funcs_action_unsubscribe($uri);

		$GLOBALS['page_data']['source'] = <<< __EOT__

[big]Вы успешно отписались от '$title' [/big]

__EOT__;

		show_page($uri);

		go($uri, false, 3);

		return true;
	}
?>
