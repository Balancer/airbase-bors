<?
    register_action_handler('new-thread-post', 'handler_new_thread_post');

    function handler_new_thread_post($uri, $action)
	{
		include_once("funcs/mail.php");

		$ht = new DataBaseHTS;
		$us = new User;

		if(!$us->data('id') || !$us->data('name'))
		{
			$GLOBALS['page_data']['title'] = "Ошибка";
			$GLOBALS['page_data']['source'] = 'Вы не зашли в систему.';

			show_page($uri);
			return true;
		}

		if(time() - $us->data("last_answer") < 10)
		{
			$GLOBALS['page_data']['title'] = "Ошибка";
			$GLOBALS['page_data']['source'] = 'Вы слишком часто пишете ответы. Подождите немного.';

			$uid = $us->data('id');
			$nick = $us->data('nick');

			$text = <<< __EOT__
Пользователь $nick (id=$uid) отправляет повторное сообщение $uri

__EOT__;

			send_mail("mail@aviaport.ru", "balancer@balancer.ru", "Дубль сообщения", $text);
			show_page($uri);
			return true;
		}

		if($us->data("last_post_hash") == md5($_POST['text']))
		{
			$GLOBALS['page_data']['title'] = "Ошибка";
			$GLOBALS['page_data']['source'] = 'Вы уже отправили это сообщение.';

			show_page($uri);
			return true;
		}

		$next = 1 + $ht->dbh->get("SELECT max(0+replace(value,'http://{$GLOBALS['cms']['conferences_host']}/{$GLOBALS['cms']['conferences_path']}/post','')) FROM hts_data_child WHERE value like '%post%'");

		$thread = "{$uri}thread$next/";
		
		$ht->nav_link($uri, $thread);

		$ht->set_data($thread, 'title', @$_POST['title']);
		$ht->set_data($thread, 'create_time', time());
		$ht->set_data($thread, 'modify_time', time());
		$ht->set_data($thread, 'author', $us->data('id'));
		$ht->set_data($thread, 'author_name', $us->data('name'));


		$post = "http://{$GLOBALS['cms']['conferences_host']}/{$GLOBALS['cms']['conferences_path']}/post".($next+1)."/";

		$ht->nav_link($thread, $post);

		$ht->set_data($post, 'source', $_POST['text']);
		$ht->set_data($post, 'title', $_POST['title']);
		$ht->set_data($post, 'create_time', time());
		$ht->set_data($post, 'modify_time', time());
		$ht->set_data($post, 'author', $us->data('id'));
		$ht->set_data($post, 'author_name', $us->data('name'));

		if(@$_POST['subscribe'])
		{
			include_once("funcs/actions/subscribe.php");
			cms_funcs_action_subscribe($thread);
		}
		
		$us->set_data("last_answer", time());
		$us->set_data("last_post_hash", md5($_POST['text']));

		go($uri);
		return true;
	}
?>
