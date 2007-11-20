<?
	include_once("Bors.php");
	include_once("Page.php");
	include_once("User.php");
	
	global $bors;
	
	$bors = &new Bors();
	
	$obj = &new Page('http://bal.aviaport.ru/help/');
	$me = &new AP_User(5458);

	echo $me->email();
	
	echo strftime("%Y-%m-%d\n", $obj->create_time());
	
//	print_r(get_class_vars('BaseObject'));
