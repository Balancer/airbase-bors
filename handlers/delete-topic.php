<?
    register_action_handler('delete-topic', 'handler_delete_topic');

    function handler_delete_topic($uri, $action)
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

		$GLOBALS['page_data']['title'] = "Удаление темы";
		
		$hts = new DataBaseHTS();
		$title = $hts->get_data('title', $uri);
		
		$GLOBALS['page_data']['source'] = <<< __EOT__
<big><span style="color:red;">Внимание! Вы пытаетесь удалить тему '<a href="$uri">$title</a>'. 
Вы уверены? После удаления тему будет невозможно восстановить!

<center><b><a href="$uri">Нет</a> &nbsp;  | &nbsp; <a href="$uri?delete-topic-do">Да</a></b></center>

</span></big>

__EOT__;

		show_page($uri);

		return true;
	}
?>
