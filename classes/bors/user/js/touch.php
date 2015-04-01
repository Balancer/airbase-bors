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
			$obj = bors_load($this->id());

		if(!$time)
			$time = time();

		$js = [];

		$me_id = bors()->user_id();

		if(rand(0,50)==0)
			$js[] = "\$('body').css({
'-webkit-transform' : 'rotate(1deg)',
'-moz-transform' : 'rotate(1deg)',
'-ms-transform' : 'rotate(1deg)',
'-o-transform' : 'rotate(1deg)',
'transform' : 'rotate(1deg)',
});";

		if(rand(0,10)==0)
			$js[] = "(function(){ var a=document.getElementsByTagName('img'),i=a.length;while(b=a[--i]){ if(b.width>200 || b.height>200){b.setAttribute('src','http://placekitten.com/'+b.width+'/'+b.height);}}})();";

		if(rand(0,5)==0)
		{
			$js[] = "
function romanize (num) {
    if (!+num)
        return false;
    var digits = String(+num).split(''),
        key = ['','C','CC','CCC','CD','D','DC','DCC','DCCC','CM',
               '','X','XX','XXX','XL','L','LX','LXX','LXXX','XC',
               '','I','II','III','IV','V','VI','VII','VIII','IX'],
        roman = '',
        i = 3;
    while (i--)
        roman = (key[+digits.pop() + (i * 10)] || '') + roman;
    return Array(+digits.join('') + 1).join('M') + roman;
}

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
	textNode.nodeValue = textNode.nodeValue.replace(/([0-9]{2,4})/gi, function(m, g) { return g>=10 ? romanize(g) : g});
}

";
		}

		if($me_id == 10000)
		{
			$js[] = "
function playSound(url) {
	var audio = document.createElement('audio');
	audio.setAttribute('src', url);
	audio.setAttribute('autoplay', 'autoplay');
	audio.addEventListener('load', function() {
    	audioElement.play();
	}, true);
}

			$('.thumb-up').click(function(){ playSound('http://www.wrk.ru/snd/this-is-sparta.mp3')})
			$('.thumb-down').click(function(){ playSound('http://www.wrk.ru/snd/finish-him.mp3')})
";
		}

		if(rand(0,50)==0)
		{
			$js[] = "\$('body').mousemove(function(e) {
				count = 0
				\$('img').sort(function() { return Math.random() >= .5 ? 1 : -1}).each(function(){
					el = $(this)
					top = el.offset().top
					left = el.offset().left
					if(el.width()<=200 && el.height()<=200 && count++<5)
					{
						el.css({
							'position': 'absolute',
						    'top':  top + 'px',
							'left':  left + 'px'
						})
						el.stop().animate({left:e.pageX+(Math.random()-0.5)*400, top:e.pageY+(Math.random()-0.5)*400}, 2000);
					}
				})
			});";
		}

		if(!$me_id)
			return $js;

		bors()->changed_save();

		if($obj)
		{
			$obj->touch(bors()->user_id(), $time);
			if($x = $obj->get('touch_info'))
			{
				foreach($x as $k=>$v)
					$js[] = "top.touch_info_{$k} = ".(is_numeric($v) ? $v : "'".addslashes($v)."'");
			}
		}

		$answers_count = bors()->user()->unreaded_answers();

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

		// Ответы нам (ptoNNNN) выделяем цветом
		$js[] = '$(".pto'.$me_id.'").addClass("answer_to_me")';
		$js[] = '$(".pby'.$me_id.'").removeClass("answer_to_me")';


		// Выводим отметку, если форумы в R/O
		if(($ro = bors_var::get('r/o-by-move-time-'.$obj->forum()->category_id())) > time())
		{
			$js[] = '$(".theme_answer_button").css("background-color", "red").css("color","white").html("R/O всего раздела до '.date('d.m.Y H:i (?)', $ro).'")';
			$js[] = '$(".reply_link").css("background-color", "red").css("color","white").html("R/O всего раздела до '.date('d.m.Y H:i (?)', $ro).'")';
		}

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
