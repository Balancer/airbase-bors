<?
	config_set('avatar_dir', '/var/www/localhost/files/forum/avatars');

	function config_set($key, $value)
	{
		$GLOBALS['cms']['config'][$key] = $value;
	}
	
	function config($key)
	{
		return @$GLOBALS['cms']['config'][$key];
	}
