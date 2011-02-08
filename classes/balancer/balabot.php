<?php

class balancer_balabot extends base_object
{
	static function external_notify()
	{
	}

	static function on_thumb_up($target)
	{
		debug_hidden_log('balabot', 'thumb_up: '.$target->debug_title());
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

//     print_r($msg);
		}
		catch (Services_Twitter_Exception $e)
		{

			echo $e->getMessage();
		}
	}
}
