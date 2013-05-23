<?php

class conf
{
	function db_access($db, $login, $password)
	{
		$GLOBALS['cms']['mysql'][$db]['login']		= $login;
		$GLOBALS['cms']['mysql'][$db]['password']	= $password;
	}

	function set($var, $value)
	{
		$GLOBALS['cms'][$var] = $value;
	}
}
