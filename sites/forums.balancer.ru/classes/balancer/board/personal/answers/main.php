<?php

class balancer_board_personal_answers_main extends balancer_board_page
{
	var $must_be_user = true;
	function template() { return 'xfile:forum/_header.html'; }

	var $title  = 'Непрочтённые ответы Вам';
	var $nav_name = 'непрочтённые ответы';
	var $auto_map = true;

	function items_per_page() { return 25; }

	static function posts_deep() { return time()-600*86400; }
	static function posts_deep2() { return time()-30*86400; }

	function posts()
	{
		$me_id = bors()->user_id();

		$answers = bors_find_all('balancer_board_post', array(
			'answer_to_user_id' => $me_id,
			'posts.poster_id<>' => $me_id,
			'order' => '-create_time',
			'inner_join' => array("topics t ON t.id = posts.topic_id"),
			'left_join' => array("topic_visits v ON (v.topic_id = t.id AND v.user_id=$me_id)"),
//			'((v.last_visit IS NULL AND posts.posted > '.$this->posts_deep2().') OR (v.last_visit < posts.posted))',
			'posts.posted > '.$this->posts_deep2(),
			'(v.last_visit IS NULL OR v.last_visit < posts.posted)',
			'page' => $this->page(),
			'per_page' => $this->items_per_page(),
//			'posts.posted>' => $this->posts_deep(),
//			'use_index' => 'posted',
		));

		return $answers;
	}

	function body_data()
	{
		$data = parent::body_data();
/*
		$data['pagination'] = $this->pages_links_list(array(
			'div_css' => 'pagination pagination-centered pagination-small',
			'li_current_css' => 'active',
			'li_skip_css' => 'disabled',
			'skip_title' => true,
		));
*/

		$pgn = $this->pages_links_nul();
		if(preg_match('!</div>$!', $pgn))
			$data['pagination'] = preg_replace('!(</div>$)!', '<a href="/personal/answers/all/" class="select_page">Все ответы за год</a></div>', $pgn);
		else
			$data['pagination'] = '<div class="pages_select"><a href="/personal/answers/all/" class="select_page">Все ответы за год</a></div>';

		return $data;
	}

	function total_items()
	{
		$me_id = bors()->user_id();

		return bors_count('balancer_board_post', array(
			'answer_to_user_id' => $me_id,
			'posts.poster_id<>' => $me_id,
			'order' => '-create_time',
			'inner_join' => array("topics t ON t.id = posts.topic_id"),
			'left_join' => array("topic_visits v ON (v.topic_id = t.id AND v.user_id=$me_id)"),
//			'((v.last_visit IS NULL AND posts.posted > '.$this->posts_deep2().') OR (v.last_visit < posts.posted))',
			'posts.posted > '.$this->posts_deep2(),
			'(v.last_visit IS NULL OR v.last_visit < posts.posted)',
//			'posts.posted>' => $this->posts_deep(),
		));
	}
}
