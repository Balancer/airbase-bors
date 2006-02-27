<?
		include_once("funcs/users.php");
		
		$us = new User;
		$user_first_name = $us->data('first_name');
		$user_last_name = $us->data('last_name');
		$user_id = $us->data('id');
		
		$tpl_vars[] = 'user_first_name';
		$tpl_vars[] = 'user_last_name';
		$tpl_vars[] = 'user_id';
?>
