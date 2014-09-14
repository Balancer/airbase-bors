<?php

class tanzpol_page extends bors_page
{
	function config_class() { return 'tanzpol_config'; }
	function template() { return 'xfile:tanzpol/bootstrap.tpl'; }

	function pre_show()
	{
//		jquery::load();
//		bors_use('/_bal/css/layout.css');
		twitter_bootstrap::load();
		return parent::pre_show();
	}

	function project() { return bors_load('tanzpol', NULL); }
}
