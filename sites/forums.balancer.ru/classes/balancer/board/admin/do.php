<?php

class balancer_board_admin_do extends balancer_board_admin_page
{
	var $auto_map = true;

	function pre_show()
	{
		if(!bors()->user() || !bors()->user()->group()->is_coordinator())
			return bors_message(ec('У Вас нет прав для выполнения этой операции'));

		if($pid = bors()->request()->data_parse('int', 'pid'))
			$post = bors_load('balancer_board_post', $pid);

		switch(bors()->request()->data('act'))
		{
			case 'logo_assign_by_post':
				$topic = $post->topic();
				$post_image_id = $post->__image(false)->id();
				$topic->set('image_id', $post_image_id);
				$topic->store();
//				echo '<xmp>'; var_dump($post_image_id, $topic->image_id(), $topic->data['image_id'], $topic->image()->id(), $topic->image()->url()); exit();

				$topic->cache_clean();
				$topic->set_modify_time(time(), true);
				$topic->store();

				$page = $topic->find_first_unvisited_post(bors()->user())
					? 'new'
					: $topic->total_pages();

				if(config('is_developer'))
				{
					r($page);
					config_set('debug_redirect_trace', true);
				}

				return go($topic->url_ex($page));
		}

		return bors_throw('Неизвестная операция ' . bors()->request()->data('act'));

		return true;
	}
}
