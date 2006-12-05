<?
    function modules_la2_clans_clanlist_totallist()
    {
		$ch = new Cache();
		if($ch->get("la2", "clanlist-total-2"))
			return $ch->last;

		$res = "";

        $db = new DataBase('l2jdb','la2', 'la2kkk');
        $list = $db->get_array("SELECT * FROM `clan_data` WHERE `clan_level` > 0 ORDER BY `clan_level` DESC, `clan_name`;");

        $res .= "<table cellSpacing=\"0\" class=\"btab\" width=\"100%\">\n";
        $res .= "<tr><th>№</th><th>Название</th><th>Уровень</th><th>Число участников</th></tr>\n";

        $n = 1;
        foreach($list as $i)
        {
			$players = $db->get("SELECT count(*) as `count` FROM `characters` WHERE `clanid` = {$i['clan_id']};");

			if($players < 1)
				continue;

            $res .= "<tr>";
            $res .= "<td>$n</td>";
            $res .= "<th><a href=\"?clan_id=".urlencode($i['clan_name'])."\">{$i['clan_name']}</a></th>";
            $res .= "<td>{$i['clan_level']}</td>";
			
			
            $res .= "<td>$players</td>";
            $res .= "</tr>\n";
            $n++;
        }
        $res .= "</table>\n";

		return $ch->set($res, 3600);
    }

	function modules_la2_clans_clanlist_clanmembers()
	{
		include_once("clanlist.inc.php");
	}

    function modules_la2_clans_clanlist_main()
	{
		if(empty($_GET['clan_id']))
			return modules_la2_clans_clanlist_totallist();
		else
			return modules_la2_clans_clanlist_clanmembers();
	}

    echo modules_la2_clans_clanlist_main();
?>
