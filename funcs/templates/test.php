<?  
    require_once('Smarty/Smarty.class.php');

	$smarty = new Smarty;


    $smarty->assign("page", 'hts:http://airbase.ru/hangar/');

    $smarty->display("/home/airbase/forums/cms/funcs/templates/test.tpl");
?>
