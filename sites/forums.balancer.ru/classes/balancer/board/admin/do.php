<?php

class balancer_board_admin_do extends balancer_board_admin_page
{
	var $auto_map = true;

	function pre_show(&$data)
	{
		if(!bors()->user() || !bors()->user()->group()->is_coordinator())
			return bors_message(ec('У Вас нет прав для выполнения этой операции'));

		if($pid = bors()->request()->data_parse('int', 'pid'))
			$post = bors_load('balancer_board_post', $pid);

		switch(bors()->request()->data('act'))
		{
			case 'logo_assign_by_post':
				$topic = $post->topic();
				$topic->set_image_id($post->__image(false)->id());
//				var_dump($post->image()->id(), $topic->image()->id(), $topic->image()->url());
				return go($post->url_in_container());
		}

		return bors_throw('Неизвестная операция ' . bors()->request()->data('act'));

		return true;
	}
}
