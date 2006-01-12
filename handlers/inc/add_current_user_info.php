<?
		include_once("funcs/users.php");
		
		$user_first_name = user_data('first_name');
		$user_last_name = user_data('last_name');
		$user_id = user_data('id');
		
		$tpl_vars[] = 'user_first_name';
		$tpl_vars[] = 'user_last_name';
		$tpl_vars[] = 'user_id';
?>
