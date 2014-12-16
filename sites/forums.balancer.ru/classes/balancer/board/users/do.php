<?php

class balancer_board_users_do extends balancer_board_page
{
	var $auto_map = true;

	function can_action() { return (bool) bors()->user(); }

	function pre_show($data)
	{
		if(!bors()->user())
			return bors_message('Только для зарегистрированных пользователей');

		if($pid = bors()->request()->data_parse('int', 'pid'))
			$post = bors_load('balancer_board_post', $pid);

		switch(bors()->request()->data('act'))
		{
			case 'get_warn':
				return self::take_warning($pid);
		}

		return bors_throw('Неизвестная операция ' . bors()->request()->data('act'));

		return true;
	}

	static function take_warning($post_id)
	{
		$warning = bors_find_first('airbase_user_warning', ['warn_object_id' => $post_id]);
		if(!$warning)
			return go_message('Ошибка: нет предупреждения у сообщения '.$post_id);

		if($warning->score() <= 0)
			return go_message('Ошибка: попытка забрать поощрительный балл '.$post_id);

		$post = bors_load('balancer_board_post', $post_id);
		$new_score = max(1, pow(3, $post->owner()->rpg_level() - bors()->user()->rpg_level()));

		if($new_score >= 10)
			return go_message('Вы не можете принять столько баллов: '.$new_score);

		$warning->set_source($warning->source().";<br/>\nШтраф у пользователя "
			.$warning->user()->title()
			.' забрал пользователь '.bors()->user()->title());
		$warning->set_user_id(bors()->user_id());
		$warning->set_score($new_score);

		$post->cache_clean();
		$post->topic()->cache_clean();
		bors()->user()->_warnings_update();
		$post->owner()->_warnings_update();

		return go_message('Штраф успешно забран', ['type' => 'success', 'go' => $post->url_in_container()]);
	}
}
