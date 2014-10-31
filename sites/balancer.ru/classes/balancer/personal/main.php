<?php

class balancer_personal_main extends bors_page
{
	var $title_ec = 'Ваш личный кабинет';
	var $nav_name_ec = 'личный кабинет';
	var $is_auto_url_mapped_class = true;

	function body_data()
	{
		return array(
			'user' => bors()->user(),
		);
	}
}
