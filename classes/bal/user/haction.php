<?php

class bal_user_haction extends bors_user_haction
{
	function url_ex($domain = NULL)
	{
		if(preg_match('/^\w+\.\w+$/', $domain))
			$domain = 'www.'.$domain;

		return ($domain ? "http://{$domain}" : config('main_site_url'))
			.'/users/private/hactions/'.$this->id();
	}
}
