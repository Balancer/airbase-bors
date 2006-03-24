<?
	include_once("../../config.php");
	include_once("../mail.php");
	
	$start = time();
	send_mail("balancer@balancer.ru", "balancer@balancer.ru", "Simple test", "Message text");
	
	echo "Mail sent in ".(time()-$start)."sec.";
?>
