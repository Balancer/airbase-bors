<?php

/**
	Тэг, включение в тексте аттача
	Примеры использования:
		[attach=173265]
		[attach=173265 size=300]
*/

class lcml_tag_single_attach extends bors_lcml_tag_single
{
	function html($params)
	{
		$attach = bors_load('balancer_board_attach', $params['attach']);
		$html = $attach->html(defval_ne($params, 'size', 640));
		return "<div class=\"clear\">&nbsp;</div>$html<div class=\"clear\">&nbsp;</div>";
	}
}
