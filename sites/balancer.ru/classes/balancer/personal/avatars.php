<?php

class balancer_personal_avatars extends bors_page
{
	var $title_ec = 'Ваши аватары';
	var $is_auto_url_mapped_class = true;

	function body_data()
	{
		return array(
			'user' => bors()->user(),
			'avatars' => bors_find_all('balancer_user_avatar', array(
				'user_id' => bors()->user_id(),
				'order' => '-create_time',
			)),
		);
	}
}
