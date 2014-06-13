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

		$js = array();

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

		$js = join("\n", $js);
		if(!$js)
			$js = 'true;';

		return $js;
	}
}
