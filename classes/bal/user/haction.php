<?php

class bal_user_haction extends bors_user_haction
{
	function url_ex($domain = NULL)
	{
		return ($domain ? "http://{$domain}" : config('main_site_url'))
			.'/users/private/hactions/'.$this->id();
	}
}
