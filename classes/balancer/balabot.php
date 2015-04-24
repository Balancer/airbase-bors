<?php

class balancer_balabot extends bors_object
{
	static function external_notify()
	{
	}

	static function on_thumb_up($target)
	{
//		$bot = bors_find_first('balancer_board_user', array('title' => 'BalaBOT'));
		$bot = bors_load('balancer_board_user', 84069);
		//balancer_balabot::send_tweet('Ты скучен, Зойдберг.');

		$twitter_class_id = class_name_to_id('bors_external_twitter');

		$prev = bors_find_first('bors_users_blogs_map', array(
			'target_class_id' => $target->extends_class_id(),
			'target_object_id' => $target->id(),
			'blog_class_id' => $twitter_class_id,
		));

		if($prev)
			return; // Уже было размещено

		$url = ' '.wrk_go::make_short_url($target);

		$tpls = array(
			array('Понравилось сообщение от %user% «', '»'),
			array('%user% интересно пишет: ', ''),
			array('Обратите внимание на пост %user% «', '»'),
			array('Очень интересно: %user%> ', ''),
			array('Хорошо пишет %user%: ', ''),
			array('Неплохо: %user%> ', ''),
			array('Согласен с %user% - ', ''),
		);

		$tpl = $tpls[rand(0, count($tpls)-1)];

		$pre  = $tpl[0];
		$post = $tpl[1];

		$owner_title = $target->owner()->title();

		$pre  = $owner_title.'> '; //str_replace('%user%', $target->owner()->title(), $pre); // $target->score()
		$post = /*$post.*/ $url;

		$limit = 140 - bors_strlen($post) - bors_strlen($pre);
		$text = strip_text($target->source(), $limit, '…', true);
		$msg = $pre.$text.$post;

		debug_hidden_log('balabot', "Limit=$limit, text lenth=".bors_strlen($text).", full length=".bors_strlen($msg).', text=|'.$msg.'|', false);

/*
		if($blog_object_id = bors_external_twitter::send($bot, $msg))
		{
			object_new_instance('bors_users_blogs_map', array(
				'target_class_id' => $target->extends_class_id(),
				'target_object_id' => $target->id(),
				'blog_class_id' => $twitter_class_id,
				'blog_object_id' => $blog_object_id,
			));
		}
*/

		// Также продублируем всем подписчикам
		$subscribed = bors_find_all('balancer_board_user', array(
			'jabber',
			'xmpp_notify_best' => true,
		));

		foreach($subscribed as $user)
		{
			$user->notify_text("Сообщение пользователя {$owner_title} было признано лучшим:\n"
				.trim(html_entity_decode(make_quote($user->title(), htmlspecialchars($target->source()), false), ENT_COMPAT, 'UTF-8'))
				."\n\n// #{$target->id()} {$target->url_for_igo()} в теме «{$target->topic()->title()}»"
			);
		}

		debug_hidden_log('balabot', "{$target->debug_title()} posted in twitter as ".@$blog_object_id, false);
	}

	static function send_tweet($msg)
	{
		$username = config('balabot.twitter.login');
		$password = config('balabot.twitter.password');

		require_once 'Services/Twitter.php';
		require_once 'HTTP/OAuth/Consumer.php';
		try {
			$twitter = new Services_Twitter();
			$oauth   = new HTTP_OAuth_Consumer(
				config('balabot.twitter.consumer_key'),
				config('balabot.twitter.consumer_secret'),
				config('balabot.twitter.auth_token'),
				config('balabot.twitter.token_secret')
			);

			$twitter->setOAuth($oauth);

			$msg = $twitter->statuses->update($msg);
//			debug_hidden_log('twitter', 'result :'.print_r($msg, true));
			return $msg;
		}
		catch (Services_Twitter_Exception $e)
		{
			debug_hidden_log('twitter', 'Exception :'.$e);
//			echo $e->getMessage();
		}
	}
}
