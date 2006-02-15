<?
    register_action_handler('news-add', 'handler_edit_news_add');
    register_action_handler('news-add-do', 'handler_edit_news_add_do');

    function handler_edit_news_add($uri, $action)
	{
		require_once("funcs/check/access.php");

		if(!check_action_access(2, $uri))
			return true;

		$hts = new DataBaseHTS;
		$uri = $hts->normalize_uri($uri);

		include_once("funcs/templates/assign.php");

		$data = array(
			'body'  =>  template_assign_data("xfile:".dirname(__FILE__)."/news-form.htm", array('main_uri' => $uri)),
			'title' => ec("Редактирование навигации страницы ").$hts->get_data($uri, 'title'),
			);

		include_once("funcs/templates/show.php");
		template_assign_and_show($uri, $data);
		return true;
	}

    function handler_edit_news_add_do($uri, $action)
	{
		$ht = new DataBaseHTS;
		$us = new User;

		if(!$us->data('id'))
		{
			$GLOBALS['page_data']['title'] = ec("Ошибка");
			$GLOBALS['page_data']['source'] = ec('Вы не зашли в систему.');

			show_page($uri);
			return true;
		}

		include_once("funcs/modules/messages.php");

		if(empty($_POST['title']))
			return error_message(ec("Вы не указали заголовок новости", false));

		if(empty($_POST['description']) && empty($_POST['text']))
			return error_message(ec("Вы должны указать текст новости или её описание", false));

		include_once("funcs/system.php");
		$year_uri  = $uri.strftime("%Y/");
		$month_uri = $year_uri.strftime("%m/");
		$day_uri   = $month_uri.strftime("%d/");
		$new_uri   = $day_uri.get_new_global_id()."/";

	//	$GLOBALS['log_level']=10;
		$ht->nav_link($uri, $year_uri);
		$ht->nav_link($year_uri, $month_uri);
		$ht->nav_link($month_uri, $day_uri);
		$ht->nav_link($day_uri, $new_uri);
//		$GLOBALS['log_level']=2;


		$ht->set_data($new_uri, 'source', @$_POST['text']);
		$ht->set_data($new_uri, 'title',  $_POST['title']);
		$ht->set_data($new_uri, 'description', @$_POST['description']);

		$ht->set_data($new_uri, 'create_time', time());
		$ht->set_data($new_uri, 'modify_time', time());
		$ht->set_data($new_uri, 'author', $us->data('id'));
		$ht->set_data($new_uri, 'author_name', $us->data('name'));


		go($new_uri);
		return true;
	}
?>
