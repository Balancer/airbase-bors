<?
	$us = new User;

	$access_level = $us->data('level');
	$tpl_vars[] = 'access_level';

	$is_moderator = $access_level > 3;
	
	$tpl_vars[] = 'is_moderator';
?>
