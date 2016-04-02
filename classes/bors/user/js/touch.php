<?php

class user_js_touch extends bors_js
{
	function pre_show()
	{
		template_nocache();

		parent::pre_show();

		$this->set_is_loaded(true);

		$time	= bors()->request()->data('time');
		$obj	= bors()->request()->data('obj');
		$page	= bors()->request()->data('page');
		$me		= bors()->user();
		$me_id	= bors()->user_id();

		if($obj)
			$obj = bors_load_uri($obj);
		else
			$obj = bors_load($this->id());

		if($obj)
			$obj->set_page($page);

		if(!$time)
			$time = time();

		$personal_html = bors_module::mod_html('balancer_board_ajax_personal', [
			'object' => $obj,
		]);

		$js = [];

		$js[] = '$("#personal-js-placeholder").html("'.str_replace("\n", " ", addslashes($personal_html)).'")';

		if(!$me_id || $me_id < 2)
		{
			$js = join("\n", $js);

			if(!$js)
				$js = 'true;';

			return $js;
		}

		if($obj)
		{
			$obj->touch($me_id, $time);
			if($x = $obj->get('touch_info'))
			{
				foreach($x as $k=>$v)
					$js[] = "top.touch_info_{$k} = ".(is_numeric($v) ? $v : "'".addslashes($v)."'");
			}
		}

		// Сохраняем результат touch(), чтобы корректно обсчиталось число непрочитанных ответов.
		bors()->changed_save();

		$answers_count = $me->unreaded_answers();

		if($answers_count > 0)
		{
			$js[] = '$("#pers_answ_cnt").html(" ('.$answers_count.')")';
			$js[] = '$("#pers_answ_cont > a").addClass("red")';
		}

/*
if(rand(0,10) == 0)
{
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
}
*/

		// Ответы нам (ptoNNNN) выделяем цветом
		$js[] = '$(".pto'.$me_id.'").addClass("answer_to_me")';
		$js[] = '$(".pby'.$me_id.'").removeClass("answer_to_me")';

		// Выводим отметку, если форумы в R/O
		if($obj
				&& ($f = $obj->get('forum'))
				&& ($ro = bors_var::get('r/o-by-move-time-'.$f->category_id())) > time())
		{
			$js[] = '$(".theme_answer_button").css("background-color", "red").css("color","white").html("R/O всего раздела до '.date('d.m.Y H:i (?)', $ro).'")';
			$js[] = '$(".reply_link").css("background-color", "red").css("color","white").html("R/O всего раздела до '.date('d.m.Y H:i (?)', $ro).'")';
		}

		/////////////////////

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
