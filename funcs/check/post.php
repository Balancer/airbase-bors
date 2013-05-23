<?php
	function check_post($uri, $text)
	{
		if(!check_post_stop_words($uri, $text))
			return false;
			
		return true;
	}

	function check_post_stop_words($uri, $text)
	{
		$hts = new DataBaseHTS();
		
		foreach(split(' ', $hts->sys_var('stop_words')) as $word)
		{
			if(preg_match("!$word!", $text))
			{
//				$GLOBALS['page_data']['title'] = "Ошибка";
//				$GLOBALS['page_data']['source'] = 'Ваше сообщение содержит стоп-слово';
//				show_page($uri);

				$us = new User();	
				$uid = $us->data('id');
				$nick = $us->data('nick');

				$text = "Пользователь $nick (id=$uid) в сообщении $uri\n\n===========\n$text\n===========\n\nиспользует стоп-слово $word";

//				send_mail("mail@aviaport.ru", "balancer@balancer.ru", "Конференция: стоп-слово!", $text);
			}
		}
	
		return true;
	}
?>