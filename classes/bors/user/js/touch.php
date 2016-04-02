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

			// All images
//			if(rand(0,10) == 0)
//				$js[] = "\$('img').addClass('mirror');";

			// Рэндомный бэкграунд
			if(rand(0,5) == 0)
			{
				$js[] = "\$('html').addClass('random-background');";
				$js[] = "\$('body').css('background-image', 'none').css('background-color', 'none');";
				$js[] = "\$('.wrapper').css('background', 'transparent');";
				$js[] = "\$('body').css('background', 'transparent');";
			}

//			if(rand(0,20)==0)
			{
			$js[] = "\$('.avatar-image img').each(function(){
				i=\$(this);
				i.attr('width',  100);
				i.attr('height', 100);
				i.parent().parent().removeAttr('style');
				i.parent().removeAttr('style');
				i.closest('div').css('height', '100px');
				i.addClass('fun-image');
				s=i.parent().attr('href')
				u = s.replace(/^.+?(\d+)\/$/, '\$1');
//				console.log('s='+s+';u='+u)
				i.attr('src', 'http://www.balancer.ru/img/blank.gif');
				i.css('background-image', 'url(http://www.balancer.ru/hi/1a/av.php?id='+u+'&s='+s+')');
				i.removeClass('mirror');
			})";
			}


if($me_id == 10000 || rand(0,2)==0)
		{
			$js[] = "

walk(\$('body').get(0));

function walk(node) {
  var child, next;

  switch (node.nodeType) {
    case 1:  // Element
    case 9:  // Document
    case 11: // Document fragment
      child = node.firstChild;
      while (child) {
        next = child.nextSibling;
        walk(child);
        child = next;
      }
      break;
    case 3: // Text node
      handleText(node);
      break;
  }
}

function handleText(textNode) {
	txt = textNode.nodeValue
	txt = txt.replace(/резидент/g, 'атр####иарх');
	txt = txt.replace(/атриарх/g, 'рез####идент');
	txt = txt.replace(/невменяемый/g, 'вменяемый');
	txt = txt.replace(/икому/g, 'екоторым');
	txt = txt.replace(/оспарив/g, 'нахвалив');
	txt = txt.replace(/Аватар/g, 'Портрет');
	txt = txt.replace(/аватар/g, 'портрет');
	txt = txt.replace(/крымча/g, 'критя');
	txt = txt.replace(/Крым/g, 'Кр####ит');
	txt = txt.replace(/Киев/g, 'Ваш####ингтон');
	txt = txt.replace(/Вашингтон/g, 'Киев');
	txt = txt.replace(/американ/g, 'укра####ин');
	txt = txt.replace(/Америк/g, 'Укра####ин');
	txt = txt.replace(/россий/g, 'японск');
	txt = txt.replace(/Росси/g, 'Яп####они');
	txt = txt.replace(/Болгар/g, 'Гер####ман');
	txt = txt.replace(/болгар/g, 'гер####ман');
	txt = txt.replace(/Герман/g, 'Мон####гол');
	txt = txt.replace(/Путин/g, 'Доктор');
	txt = txt.replace(/Украин/g, 'Ан####тарктид');
	txt = txt.replace(/украинс/g, 'ант####арктичес');
	txt = txt.replace(/сирий/g, 'нарний');
	txt = txt.replace(/Сири/g, 'Нарни');
	txt = txt.replace(/бред(?=([^а-я]))/g, 'бурлеск');
	txt = txt.replace(/Цыган/g, 'Центавриан');
	txt = txt.replace(/цыган/g, 'центавриан');
	txt = txt.replace(/координа/g, 'махина');
	txt = txt.replace(/Координа/g, 'Махина');
	txt = txt.replace(/Модера/g, 'Махина');
	txt = txt.replace(/модера/g, 'махина');
	txt = txt.replace(/мигрант/g, 'зерг');
	txt = txt.replace(/Мигрант/g, 'Зерг');
	txt = txt.replace(/Бежен/g, 'Сажен');
	txt = txt.replace(/бежен/g, 'сажен');
	txt = txt.replace(/ФРГ/g, 'МНР');


	txt = txt.replace(/####/g, '');
	textNode.nodeValue = txt
}

";
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
