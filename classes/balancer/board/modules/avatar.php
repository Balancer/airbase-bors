<?php

/**
	Вывод блока картинки аватара

	Передаются параметры [по умолчанию]:
		geo [100x100] - геометрия конечного блока
		Одно из следующего:
			- avatar_id - id аватара
			- user_id - id пользователя
*/

class balancer_board_modules_avatar extends bors_module
{
	function body_data()
	{
		$geo = $this->args('geo', '100x100');
		$avatar = $this->args('avatar');
		$user   = $this->args('user');
		$avatar_id = $this->args('avatar_id');
		$user_id = $this->args('user_id');
		$title_css = $this->args('title_css');
		$title_style = $this->args('title_style');

		if(!$avatar && $avatar_id)
			$avatar = bors_load('balancer_board_avatar', $avatar_id);
		if(!$user && $user_id)
			$user = bors_load('balancer_board_user', $user_id);
		if(!$avatar && $user)
			$avatar = $user->avatar();

		if(preg_match('/^(\d*)x(\d*)/', $geo, $m))
		{
			$block_w = intval($m[1]);
			$block_h = intval($m[2]);
		}

		if($avatar)
		{
			$image = $avatar->image()->thumbnail($geo);
			$image->wxh(); // чтобы произошёл перерасчёт параметров, если их ещё нет.
			$height = $image->height();
			$width  = $image->width();
			$margin_w = max(0, floor(($block_w - $width)/2));
			$margin_h = max(0, floor(($block_h - $height)/2));
		}

		$show_title = $this->args('show_title', true);
		$show_group = $this->args('show_group', $block_w >= 100);

		return array_merge(parent::body_data(), compact(
			'user', 'geo',
			'height', 'width',
			'margin_w', 'margin_h',
			'block_w', 'block_h',
			'image', 'avatar',
			'show_group', 'show_title',
			'title_css', 'title_style'
		));
	}
}
