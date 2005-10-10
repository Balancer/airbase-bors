<?
    register_action_handler('new-thread', 'handler_new_thread_form');

    function handler_new_thread_form($uri, $action)
	{
		require_once('funcs/templates/show.php');

		$us = new User;

		if(!$us->data('id') || !$us->data('name'))
		{
			$GLOBALS['page_data']['title'] = "Ошибка";
			$GLOBALS['page_data']['source'] = 'Вы не зашли в систему.';

			show_page($uri);
			return true;
		}

		$data['conferences_uri'] = "http://{$GLOBALS['cms']['conferences_host']}/{$GLOBALS['cms']['conferences_path']}";
		$data['title'] = "Создание новой темы";
		$data['caching'] = false;
		$data['modify_time'] = time();
		$data['source'] = '';
		$us = new User;
		$data['user_first_name'] = $us->data('first_name');
		$data['user_last_name'] = $us->data('last_name');

		template_assign_and_show($uri, "http://{$GLOBALS['cms']['conferences_host']}/cms/templates/new-thread-form/", $data);

		//'[module forums/new-topic-form]';

		show_page($uri);

		return true;
	}
?>
