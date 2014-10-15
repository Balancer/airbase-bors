<?php

class balancer_personal_accounts extends base_page
{
	var $title_ec = 'Ваши учётные записи';
	var $is_auto_url_mapped_class = true;

	function body_data()
	{
		return array(
			'user' => bors()->user(),
		);
	}
}
