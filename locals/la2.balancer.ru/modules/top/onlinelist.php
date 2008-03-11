<?
    function modules_top_onlinelist_main($title, $database)
    {
		$ch = &new Cache();
		if($ch->get('la2', "onlinelist-$database"))
			return $ch->last;
	
        $hts = &new DataBase($database,'la2', 'la2kkk');
        $list = $hts->get_array("SELECT cl.clan_name, ch.* FROM `characters` `ch` LEFT JOIN `clan_data` `cl` ON (cl.clan_id = ch.clanid) WHERE `online` > ".(0*(time()-1000))." ORDER BY `onlinetime` DESC, `char_name`;");

		$res = "";

        $res .= "<table cellSpacing=\"0\" class=\"btab\" width=\"100%\">\n";
		$res .= "<tr><th colSpan=\"6\">$title</th></tr>\n";
        $res .= "<tr><th>№</th><th>Имя</th><th>Уровень</th><th>Класс</th></th><th>Клан</th><th>Карма</th></tr>\n";

        $n = 1;
        foreach($list as $i)
        {
            $classes = $hts->get_array("SELECT * FROM `character_subclasses` WHERE `char_obj_id` = ".addslashes($i['obj_Id']));
			
//			print_r($classes);
			
			$span = sizeof($classes);
			$span = $span > 1 ? " rowSpan=\"$span\"" : "";

			if($i['karma'] > 200)
			{
				$bc="<span style=\"color:red;\">";
				$ec="</span>";
			}
			else
			{
				$bc="";
				$ec="";
			}

            $res .= "<tr>";
            $res .= "<td$span>$n</td>";
            $res .= "<th$span>$bc{$i['char_name']}&nbsp;$ec";
			if($i['sex'])
            	$res .= "<img src=\"http://la2.balancer.ru/images/woman.gif\" width=\"11\" height=\"11\" valign=\"middle\">";
			else
            	$res .= "<img src=\"http://la2.balancer.ru/images/man.gif\" width=\"11\" height=\"11\" valign=\"middle\">";
			$res .= ($i['accesslevel']>10 ? " <span style=\"7pt;\">(GM)</span>":"")."</th>";

			$subs = array();
			$iter = 0;
			
			foreach($classes as $subclass)
			{
//            $GLOBALS['log_level'] = 9;
	            $cs = $hts->get("SELECT * FROM `class_list` WHERE `id` = ".$subclass['class_id']);
//            $GLOBALS['log_level'] = 2;
				
				$add = "<td>{$subclass['level']}</td>";
				
        	    list($r, $class) = split("_", $cs['class_name']);
        	    $race = array('H'=>'Human', 'E'=>'Elven', 'DE'=>'Dark Elven', 'O'=>'Orc', 'D'=>'Dwarven');
	            $add .= "<td><nobr>{$race[$r]} $class</nobr></td>";
				if($iter)
					$subs[] = $add;
				else
					$res .= $add;
					
				$iter++;
			}

   	        $res .= $i['clan_name'] ? "<td$span><a href=\"/clans/?clan_id=".urlencode($i['clan_name'])."\">{$i['clan_name']}</a></td>" : "<td$span>&nbsp;</td>";
            $res .= "<td$span>$bc{$i['karma']}$ec</td>";
            $res .= "</tr>\n";
			
			foreach($subs as $sub)
				$res .= "<tr>$sub</tr>\n";
			
            $n++;
        }

        $res .= "<tr><th colSpan=\"6\">Всего онлайн: ".sizeof($list)."</th></tr>\n";
        $res .= "</table>\n";

		return $ch->set($res, 600);
    }

    echo modules_top_onlinelist_main(ec("Основной"), "l2jdb");
    echo modules_top_onlinelist_main(ec("Тестовый"), "l2jtestdb");
?>

<ul>
<li>Данные обновляются раз в 10 минут.</li>
</ul>
