<?
    function module_top_online_main()
    {
        $hts = new DataBase('l2jdb','la2', 'la2kkk');
        echo $hts->get("SELECT count(*) AS `count` FROM `characters` WHERE `online` > 0");
		echo "+";
        echo $hts->get("SELECT count(*) AS `count` FROM l2jtestdb.characters WHERE `online` > 0");
    }

    module_top_online_main();
?>

