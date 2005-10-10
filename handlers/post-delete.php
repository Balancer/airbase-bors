<?
    register_action_handler('post-delete', 'handler_action_post_delete');

    function handler_action_post_delete($uri, $action)
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

		$GLOBALS['page_data']['title'] = "Удаление сообщения";
		
		$hts = new DataBaseHTS();

		$message = $hts->get_data($uri, 'source');
		
		$GLOBALS['page_data']['source'] = <<< __EOT__
[big][red]Внимание! Вы пытаетесь удалить сообщение:[/red][/big]
[pre]{$message}[/pre]
Вы уверены? После удаления тему будет невозможно восстановить!

[center][b][big][$uri|Нет] | [$uri?post-delete-do|Да][/big][/b][/center]
__EOT__;

		show_page($uri);

		return true;
	}
?>
