<?
    function modules_top_money_main()
    {
		$ch = new Cache();
		if($ch->get("la2", "top-money"))
			return $ch->last;

        include_once('funcs/DataBase.php');
        $hts = new DataBase('l2jdb','la2', 'la2kkk');
        $max = $hts->get("SELECT MAX(t.sum) FROM `total` t LEFT JOIN `characters` p ON (t.owner_id = p.obj_Id) WHERE p.accesslevel>=0 AND p.accesslevel < 30;");
        $list = $hts->get_array("SELECT t.*, p.* FROM `total` t LEFT JOIN `characters` p ON (t.owner_id = p.obj_Id) WHERE p.accesslevel>=0 AND p.accesslevel < 30 ORDER BY `sum` DESC LIMIT 50;");

		$res = "";	

        $res .= "<table cellSpacing=\"0\" class=\"btab\" width=\"100%\">\n";
        $res .= "<tr><th>№</th><th>Имя пользователя</th></th><th>Богатство</tr>\n";

        $n = 1;
        foreach($list as $i)
        {
//            $GLOBALS['log_level'] = 9;
            $cs = $hts->get("SELECT * FROM `characters` WHERE `obj_Id` = ".$i['owner_id']);
//            $GLOBALS['log_level'] = 2;

            $res .= "<tr>";
            $res .= "<td>$n</td>";
            $res .= "<td><b>{$cs['char_name']}</b></td>";
			$res .= "<td><span style=\"background-color: #A1A6C0;\">".str_repeat('&nbsp;', $i['sum']*50/$max)."</span></td>\n";
//            $res .= "<td>$r $class</td>";

            $res .= "</tr>\n";
            $n++;
        }

        $res .= "</table>\n";

		return $ch->set($res, 86400);
    }

    echo modules_top_money_main();
?>
