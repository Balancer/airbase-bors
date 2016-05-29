<?php
    function clan_list($clan_id)
    {
		$ch = new bors_cache();
		if($ch->get("la2", "clanlist-$clan_id-v3"))
			return $ch->last;

		$res = "";

        include_once("{$_SERVER['DOCUMENT_ROOT']}/cms/config.php");
        include_once('funcs/DataBase.php');
        $db = new driver_mysql('l2jdb','la2', 'la2kkk');

    	if(!intval($clan_id) || intval($clan_id) < 1000000)
		{
			$clan_name = $clan_id;
	        $clan_id = $db->get("SELECT `clan_id` FROM `clan_data` WHERE `clan_name` LIKE '".addslashes($clan_id)."';");
		}
		else
		{
			$clan_id = intval($clan_id);
	        $clan_name = $db->get("SELECT `clan_name` FROM `clan_data` WHERE `clan_id` = $clan_id;");
		}

    	if(!$clan_name || !$clan_id)
    	{
    		$res .= "No clan id or name";
    		return;
    	}

		$leader_id = $db->get("SELECT `leader_id` FROM `clan_data` WHERE `clan_id` = $clan_id;");

        $list = $db->get_array("SELECT *
			FROM `characters` 
				LEFT JOIN character_subclasses ON obj_Id = char_obj_id
			WHERE `clanid` = $clan_id  
			ORDER BY `level` DESC, `char_name`;");

		$res .= "<p><a href=\"http://la2.balancer.ru/clans/?\">На страницу кланов &#187;&#187;&#187;</a></p>";

		$res .= "<h2>$clan_name</h2>";

        $res .= "<table cellSpacing=\"0\" class=\"btab\">\n";
        $res .= "<tr><th>№</th><th>Имя</th><th>Уровень</th><th>Класс</th><th>Статус</th></tr>\n";

        $n = 1;
        foreach($list as $i)
        {
            $cs = $db->get("SELECT * FROM `class_list` WHERE `id` = ".$i['class_id']);

            $res .= "<tr>";
            $res .= "<td>$n</td>";

			$res .= "<td><b>{$i['char_name']}&nbsp;</b>";

			if($i['obj_Id'] == $leader_id)
	            $res .= "&nbsp<img src=\"http://la2.balancer.ru/images/star.gif\" width=\"16\" height=\"16\" valign=\"middle\">";

			if($i['sex'])
            	$res .= "<img src=\"http://la2.balancer.ru/images/woman.gif\" width=\"11\" height=\"11\" valign=\"middle\">";
			else
            	$res .= "<img src=\"http://la2.balancer.ru/images/man.gif\" width=\"11\" height=\"11\" valign=\"middle\">";
			$res .= "</b></td>";

            $res .= "<td>{$i['level']}</td>";
            list($r, $class) = split("_", $cs['class_name']);
            $race = array('H'=>'Human', 'E'=>'Elven', 'DE'=>'Dark Elven', 'O'=>'Orc', 'D'=>'Dwarven');
            $res .= "<td>{$race[$r]} $class</td>";
//            $res .= "<td>$r $class</td>";

			$res .= "<td>".(($i['online'] > 0*(time()-1000)) ? '<font color="green">online</font>':'&nbsp;')."</td>";

            $res .= "</tr>\n";
            $n++;
        }

        $res .= "<tr><th colSpan=\"6\">Всего персонажей: ".sizeof($list)."</th></tr>\n";
        $res .= "</table>\n";

		$res .= "<p><a href=\"http://la2.balancer.ru/clans/?\">На страницу кланов &#187;&#187;&#187;</a></p>";

		return $ch->set($res, 3600);
    }

    echo clan_list($_GET['clan_id']);
?>

<ul>
<li>Данные обновляются один раз в час.</li>
</ul>
