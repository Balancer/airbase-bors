<?
    register_action_handler('reply-post', 'handler_reply_post');

    function handler_reply_post($uri, $action)
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

//			exit("end");
			show_page($uri);
			return true;
		}

		include_once("funcs/check/post.php");
		
		if(!check_post($uri, $_POST['text']))
			return true;
		
		if($us->data("last_post_hash") == md5($_POST['text']))
		{
			$GLOBALS['page_data']['title'] = "Ошибка";
			$GLOBALS['page_data']['source'] = 'Вы уже отправили это сообщение.';

			show_page($uri);
			return true;
		}

		$next = 1 + $ht->dbh->get("SELECT max(0+replace(value,'http://{$GLOBALS['cms']['conferences_host']}/{$GLOBALS['cms']['conferences_path']}/post','')) FROM hts_data_child WHERE value like '%post%'");

		$post = "http://{$GLOBALS['cms']['conferences_host']}/{$GLOBALS['cms']['conferences_path']}/post".($next+1)."/";

		$ht->nav_link($uri, $post);
		
		$ht->set_data($post, 'source', $_POST['text']);
		$ht->set_data($post, 'title', @$_POST['title']);
		$ht->set_data($post, 'create_time', time());
		$ht->set_data($uri,  'modify_time', time());
		$ht->set_data($post, 'modify_time', time());
		$ht->set_data($post, 'author', $us->data('id'));
		$ht->set_data($post, 'author_name', $us->data('name'));

		$title = $ht->get_data($uri, 'title');

		foreach($ht->get_data_array($uri, 'subscribe', 'id, visited', 'visited>0 AND value') as $user)
		{
			// Вырезаем ID юзера из URI и создаём его объект
			$user_id = preg_replace("!^.+/~(\d+)/$!", "$1", $user['id']);
			$us2 = new User($user_id);
			$nick = $us2->data('nick');

			`echo {$user['id']}, {$user['visited']}, $user_id=$nick >> /tmp/222/users.txt`;

			if($user_id == $us->data('id'))
				continue;


			$text = <<< __EOT__
Здравствуйте, $nick!

В конференции АвиаПорт.Ру обновилась тема "$title"

{$_POST['text']}

// Ссылка на тему: {$uri}

__EOT__;

			send_mail("mail@aviaport.ru", $us2->data('email'), "Обновление темы конференции АвиаПорт.Ру", $text);
		}

		if(@$_POST['subscribe'])
		{
			include_once("funcs/actions/subscribe.php");
			cms_funcs_action_subscribe($uri);
		}
		
		$ht->update_data($uri, 'subscribe', array('visited'=>0), 'value');
		$ht->update_data($uri, 'subscribe', array('visited'=>time()), "id='".addslashes($us->get_page())."' AND value");

		$us->set_data("last_answer", time());
		$us->set_data("last_post_hash", md5($_POST['text']));

//		exit("en");

		go($uri);
		return true;
	}
?>
