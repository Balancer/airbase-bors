<?
    function modules_top_list_main()
    {
		$cache = new Cache();
		
		if($cache->get('la2', 'top-100-main'))
			return $cache->last;
	
		$res = "";
	
        include_once('funcs/DataBase.php');
        $hts = new DataBase('l2jdb','la2', 'la2kkk');
        $list = $hts->get_array("
			SELECT cl.clan_name, ch.char_name, ch.sex, ac.lastactive, s.class_id, s.level
			FROM character_subclasses s
				LEFT JOIN  `characters` `ch` ON s.char_obj_id = ch.obj_Id AND lastAccess > ".(time()-86400*31)."
				LEFT JOIN `clan_data` `cl` ON (cl.clan_id = ch.clanid) 
				LEFT JOIN accounts ac ON (ac.login = ch.account_name) 
			WHERE ch.obj_Id IS NOT NULL 
				AND `accesslevel` < 100 
			ORDER BY `level` DESC, `char_name` 
			LIMIT 100;");

        $res .=  "<table cellSpacing=\"0\" class=\"btab\" width=\"100%\">\n";
        $res .=  "<tr><th>№</th><th>Имя</th><th>Уровень</th><th>Класс</th></th><th>Клан</th><th>Был в игре</th></tr>\n";

        $n = 1;
        foreach($list as $i)
        {
//            $GLOBALS['log_level'] = 9;
			$cs = $hts->get("SELECT * FROM `class_list` WHERE `id` = ".$i['class_id']);
//            $GLOBALS['log_level'] = 2;

            $res .=  "<tr>";
            $res .=  "<td>$n</td>";
            $res .=  "<th>{$i['char_name']}&nbsp;";
			
			if($i['sex'])
            	$res .=  "<img src=\"http://la2.balancer.ru/images/woman.gif\" width=\"11\" height=\"11\" valign=\"middle\">";
			else
            	$res .=  "<img src=\"http://la2.balancer.ru/images/man.gif\" width=\"11\" height=\"11\" valign=\"middle\">";
			$res .= "</th>";
			
            $res .=  "<td>{$i['level']}</td>";
            @list($r, $class) = @split("_", $cs['class_name']);
            $race = array('H'=>'Human', 'E'=>'Elven', 'DE'=>'Dark Elven', 'O'=>'Orc', 'D'=>'Dwarven');
            $res .=  "<td><nobr>".@$race[$r]." $class</nobr></td>";
            $res .=  $i['clan_name'] ? "<td><a href=\"/clans/?clan_id=".urlencode($i['clan_name'])."\">{$i['clan_name']}</a></td>" : "<td>&nbsp;</td>";

			$last = $i['lastactive'];
			
			if($last>0)
				$res .=  "<td><nobr><span style=\"font-size: 8pt;\">".strftime("%Y-%m-%d %H:%M", $last)."</span></nobr></td>";
			else
				$res .=  "<td>&nbsp;</td>";
            $res .=   "</tr>\n";
            $n++;
        }

        $list = $hts->get("SELECT COUNT(*) FROM `characters`");

        $res .= "<tr><th colSpan=\"6\">Всего персонажей за последние полгода: $list</th></tr>\n";
        $res .= "</table>\n";

		return $cache->set($res, 3600);
    }

    echo modules_top_list_main();
?>
<ul>
<li>Показаны песонажи, игравшие за последний месяц</li>
<li>Список обновляется один раз в час</li>
</ul>
