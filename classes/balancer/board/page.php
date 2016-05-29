<?php

class balancer_board_page extends bors_page
{
	function _config_class_def() { return 'balancer_board_config'; }
	function template() { return 'xfile:forum/page.html'; }
	var $auto_map = true;

	function pre_show()
	{
		if($this->get('must_be_user'))
		{
			template_noindex();
			if(!bors()->user())
				return bors_message(ec('Эта страница доступна только для авторизованных пользователей'));
		}

		return parent::pre_show();
	}

	function is_public_access() { return true; }
	function can_adsense() { return !preg_match('/balancer\.ru/', $_SERVER['HTTP_HOST']); }
	function can_yandex_direct() { return config('ad.yandex.enabled') && preg_match('/forums\.balancer\.ru/', $_SERVER['HTTP_HOST']); }
}
