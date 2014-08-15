<?php

class user_js_touch extends bors_js
{
	function pre_show()
	{
		template_nocache();

		parent::pre_show();

		$this->set_is_loaded(true);

		$time = bors()->request()->data('time');
		$obj  = bors()->request()->data('obj');

		if($obj)
			$obj = bors_load_uri($obj);
		else
			$obj = object_load($this->id());

		if(!$time)
			$time = time();

		$js = [];

		if($obj)
		{
			$obj->touch(bors()->user_id(), $time);
			if($x = $obj->get('touch_info'))
			{
				foreach($x as $k=>$v)
					$js[] = "top.touch_info_{$k} = ".(is_numeric($v) ? $v : "'".addslashes($v)."'");
			}
		}

		$me_id = bors()->user_id();
		if(!$me_id)
			return $js;

		$answers_count = bors_count('balancer_board_post', array(
			'answer_to_user_id' => $me_id,
			'posts.poster_id<>' => $me_id,
			'order' => '-create_time',
			'inner_join' => array("topics t ON t.id = posts.topic_id"),
			'left_join' => array("topic_visits v ON (v.topic_id = t.id AND v.user_id=$me_id)"),
			'((v.last_visit IS NULL AND posts.posted > '.(time()-30*86400).') OR (v.last_visit < posts.posted))',
			'posts.posted>' =>  time()-600*86400,
		));

		if($answers_count > 0)
		{
			$js[] = '$("#pers_answ_cnt").html(" ('.$answers_count.')")';
			$js[] = '$("#pers_answ_cont > a").addClass("red")';
		}
/*
		$user_ids = explode(',', bors()->request()->data('user_ids'));
		$relations_to = [];
		foreach(bors_find_all('balancer_board_users_relation', array(
				'from_user_id' => $me_id,
				'to_user_id IN' => $user_ids,
		)) as $rel)
			$relations_to[$rel->to_user_id()] = touch_rel_color($rel->score());

		$relations_from = [];
		foreach(bors_find_all('balancer_board_users_relation', array(
				'to_user_id' => $me_id,
				'from_user_id IN' => $user_ids,
		)) as $rel)
			$relations_from[$rel->from_user_id()] = touch_rel_color($rel->score());

		$js[] = "function avatar_gradient(user_id, start_color, end_color) {
	\$('.avatar-'+user_id)
		.css('background-image', '-webkit-linear-gradient('+start_color+', '+end_color+')')
		.css('background-image', '-moz-linear-gradient('+start_color+', '+end_color+')')
		.css('background-image', '-o-linear-gradient('+start_color+', '+end_color+')')
		.css('background-image', '-ms-linear-gradient('+start_color+', '+end_color+')')
		.css('background-image', 'linear-gradient('+start_color+', '+end_color+')')
}";

		foreach($user_ids as $user_id)
		{
			if(!empty($relations_to[$user_id]) || !empty($relations_from[$user_id]))
			{
				$to		= empty($relations_to[$user_id])   ? '#fff' : $relations_to[$user_id];
				$from	= empty($relations_from[$user_id]) ? '#fff' : $relations_from[$user_id];
				$js[] = "avatar_gradient({$user_id}, '{$to}', '{$from}')";
			}
		}
*/
		$js = join("\n", $js);

		if(!$js)
			$js = 'true;';

		return $js;
	}
}

function touch_rel_color($score)
{
	if(!$score)
		return '#fff';

	$col = sprintf("%02x", max(128, 255-3*abs($score)));

	if($score > 0)
		return "#{$col}ff{$col}";

	return "#ff{$col}{$col}";
}
