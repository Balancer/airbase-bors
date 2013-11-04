<?php

class balancer_board_page extends bors_page
{
	function config_class() { return 'balancer_board_config'; }
	function template() { return 'xfile:forum/page.html'; }
	function auto_map() { return true; }

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
}
