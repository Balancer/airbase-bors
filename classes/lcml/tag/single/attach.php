<?php

/**
	Категории: Файлы, Изображения

	Тег включения в текст аттача. Если не указан размер аттача, то по умолчанию
	показывается крупный вариант (640x640, но цифра может и поменяться).

	Примеры использования:
		[attach=173265]
		[attach=173265 size=300]
*/

class lcml_tag_single_attach extends bors_lcml_tag_single
{
	function html($text, &$params)
	{
		$attach = bors_load('balancer_board_attach', $params['attach']);

		if(!$attach)
			return "[{$params['orig']}]";

		if(($post = @$params['self']) && balancer_board_post::is_post($post))
			balancer_board_posts_object::register_object($post, $attach, true);

//		if(config('is_developer')) { var_dump($post->id()); exit(__CLASS__); }

		$html = $attach->html(defval_ne($params, 'size', 640));
		return "<div class=\"clear\">&nbsp;</div>$html<div class=\"clear\">&nbsp;</div>";
	}
}
