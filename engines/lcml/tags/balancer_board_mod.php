<?php

function lt_balancer_board_mod($args)
{
	switch($f = defval($args, 'func'))
	{
		case 'mail_domain_blacklist':
			$bl = explode(' ', config('mail.to.blacklist.domains'));
			if(!config('mail.to.blacklist.domains'))
				return ec('список блокированных доменов пуст');
			$bl = array_map(function($x) { return '@'.$x; }, $bl);
			return join("<br/>", $bl);
		default:
			return "Unknown function $f";
	}
}
