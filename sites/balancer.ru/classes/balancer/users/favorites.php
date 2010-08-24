<?php

class balancer_users_favorites extends bors_page
{
	function title() { return ec('Ваше избранное'); }
	function nav_name() { return ec('ваше избранное'); }
	function config_class() { return 'balancer_board_config'; }

	function is_auto_url_mapped_class() { return true; }

	function pre_show()
	{
		$this->tools()->use_ajax();
		return parent::pre_show();
	}

	function local_data()
	{
		$favorites = objects_array('bors_user_favorite', array(
			'user_id' => bors()->user_id(),
			'order' => '-create_time',
		));

		return array_merge(parent::local_data(), array(
			'items' => bors_field_array_extract($favorites, 'target'),
		));
	}
}
