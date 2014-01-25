<?php

/*
	WebCache class
	Вызов вида:
		http://www.balancer.ru/wc/?https://lh3.ggpht.com/BlEh1zFD7dpIl9LGUpL8OHH96XmM2JxEty8oNaA-CMTDQfwTbp2G1cqAHXZD8Nqpyw=w300-rw
		// via 
*/

class balancer_wc extends bors_object
{
	function pre_show()
	{
		$original_url = $this->id();
		$url_key = bors_substr(blib_urls::norm($original_url), 0, 255);
		$object = bors_find_first('balancer_webcache_object', ['original_url_key' => $url_key]);
		if(!$object)
		{
			$object = bors_new('balancer_webcache_object', [
				'original_url_full' => $original_url,
				'original_url_key' => $url_key,
			]);

			return 'ok';
		}

		if($u = $object->local_url())
			return go($u, true);

		$handler = $object->handler();
		$object->set_local_url($u = $handler->url());
		return go($u, true);
	}
}
