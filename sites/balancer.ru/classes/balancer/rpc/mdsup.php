<?php

class balancer_rpc_mdsup extends balancer_page
{
	function can_be_empty() { return true; }
	var $auto_map = true;

	function pre_parse()
	{
		bors_debug::syslog('00rpc', "Github");
		bors_debug::syslog('00rpc', "payload=".print_r(json_decode(@$_POST['payload'], true), true));
		// https://github.com/kzykhys/PHPGit
		$git = new PHPGit\Git();
		$git->setRepository('/var/www/www.balancer.ru/markdown-static');
		$git->pull();
		return parent::pre_parse();
	}
}
