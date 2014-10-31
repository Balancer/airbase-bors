<?php

class balancer_board_personal_favorites extends bors_page
{
	function title() { return ec('Ваше избранное'); }
	function nav_name() { return ec('избранное'); }
	function is_auto_url_mapped_class() { return true; }
	function template() { return 'forum/_header.html'; }

	function body_data()
	{
		$favorites = objects_array('balancer_board_favorite', array(
			'owner_id' => bors()->user_id(),
			'order' => '-modify_time',
		));

		bors_objects_targets_preload($favorites);

		return array(
			'favorites' => $favorites,
		);
	}
}
