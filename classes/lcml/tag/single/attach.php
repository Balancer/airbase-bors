<?php

/**
	Категории: Файлы, Изображения

	Тэг включения в текст аттача. Если не указан размер аттача, то по умолчанию
	показывается крупный вариант (640x640, но цифра может и поменяться).

	Примеры использования:
		[attach=173265]
		[attach=173265 size=300]
*/

class lcml_tag_single_attach extends bors_lcml_tag_single
{
	function html($params)
	{
		$attach = bors_load('balancer_board_attach', $params['attach']);

		if(($post = @$params['self']) && balancer_board_post::is_post($post))
			balancer_board_posts_object::register_object($post, $attach);

		$html = $attach->html(defval_ne($params, 'size', 640));
		return "<div class=\"clear\">&nbsp;</div>$html<div class=\"clear\">&nbsp;</div>";
	}
}
