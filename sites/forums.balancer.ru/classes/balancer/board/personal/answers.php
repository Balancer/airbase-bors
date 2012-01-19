<?php

bors_function_include('time/smart_time');

class balancer_board_personal_answers extends base_page
{
	function title() { return ec('Ответы на Ваши сообщения за последние 3 месяца'); }
	function nav_name() { return ec('ответы'); }
	function is_auto_url_mapped_class() { return true; }
	function template() { return 'forum/_header.html'; }

	function items_per_page() { return 50; }

	function local_data()
	{
		$answers = objects_array('balancer_board_post', array(
			'answer_to_user_id' => bors()->user_id(),
			'answer_to_user_id>' => 0,
			'order' => '-create_time',
			'page' => $this->page(),
			'per_page' => $this->items_per_page(),
			'create_time>' => time()-90*86400,
			'use_index' => 'posted',
		));

		return array(
			'posts' => $answers,
		);
	}

	function total_items()
	{
		return objects_count('balancer_board_post', array(
			'answer_to_user_id' => bors()->user_id(),
			'answer_to_user_id>' => 0,
			'create_time>' => time()-90*86400,
		));
	}

	function url_engine() { return 'url_calling2'; }
}
