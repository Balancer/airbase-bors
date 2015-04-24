<?php

bors_function_include('time/smart_time');

class balancer_board_personal_answers_all extends balancer_board_page
{
	function title() { return ec('Все ответы на Ваши сообщения за последний год'); }
	function nav_name() { return ec('все ответы за год'); }
	function is_auto_url_mapped_class() { return true; }
	function template() { return 'forum/_header.html'; }

	function pre_show()
	{
		$me = bors()->user();
		if(!$me || $me->is_banned())
			return bors_message('У Вас нет доступа к этому ресурсу');

		return parent::pre_show();
	}

	function items_per_page() { return 50; }

	function body_data()
	{
		$me_id = bors()->user_id();

		$answers = bors_find_all('balancer_board_posts_pure', array(
			'answer_to_user_id' => $me_id,
			'posts.poster_id<>' => $me_id,
			'order' => '-create_time',
			'page' => $this->page(),
			'per_page' => $this->items_per_page(),
			'create_time>' => time()-366*86400,
//			'use_index' => 'posted',
			'by_id' => true,
		));

		return array(
			'posts' => bors_find_all('balancer_board_post', array('id IN' => array_keys($answers), 'order' => '-create_time')),
		);
	}

	function total_items()
	{
		$me_id = bors()->user_id();

		return bors_count('balancer_board_posts_pure', array(
			'answer_to_user_id' => $me_id,
			'posts.poster_id<>' => $me_id,
			'create_time>' => time()-366*86400,
		));
	}

	function url_engine() { return 'url_calling2'; }
}