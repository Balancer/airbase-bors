<?php

class airbase_pages_zim extends bors_pages_zim
{
	function webroot() { return '/var/www/airbase.ru/bors-site/webroot'; }
	function host() { return 'http://www.airbase.ru'; }

	function pre_show()
	{
		twitter_bootstrap::load();
//		use_bootstrap();
		return parent::pre_show();
	}
}
