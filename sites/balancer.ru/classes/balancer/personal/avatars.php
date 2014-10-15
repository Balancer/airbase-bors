<?php

class balancer_personal_avatars extends base_page
{
	var $title_ec = 'Ваши аватары';
	var $is_auto_url_mapped_class = true;

	function body_data()
	{
		return array(
			'user' => bors()->user(),
			'avatars' => objects_array('balancer_user_avatar', array(
				'user_id' => bors()->user_id(),
				'order' => '-create_time',
			)),
		);
	}
}
