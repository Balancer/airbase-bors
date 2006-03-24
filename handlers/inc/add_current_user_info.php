<?
		include_once("funcs/users.php");
		
		$us = new User;
		$user_first_name = $us->data('first_name');
		$user_last_name = $us->data('last_name');
		$user_id = $us->data('id');
		$user_name = $us->data('name');
		
		$tpl_vars[] = 'user_first_name';
		$tpl_vars[] = 'user_last_name';
		$tpl_vars[] = 'user_name';
		$tpl_vars[] = 'user_id';
?>
