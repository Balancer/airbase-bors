<?
    function clan_list($clan_id)
    {
        include_once("{$_SERVER['DOCUMENT_ROOT']}/cms/config.php");
        include_once('funcs/DataBase.php');
        $db = new DataBase('l2jdb','la2', 'la2kkk');

    	if(!intval($clan_id))
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
    		echo "No clan id or name";
    		return;
    	}

		$leader_id = $db->get("SELECT `leader_id` FROM `clan_data` WHERE `clan_id` = $clan_id;");

        $list = $db->get_array("SELECT * FROM `characters` WHERE `clanid` = $clan_id  ORDER BY `level` DESC, `char_name`;");

		echo "<p><a href=\"http://la2.balancer.ru/clans/?\">На страницу кланов &#187;&#187;&#187;</a></p>";

		echo "<h2>$clan_name</h2>";

        echo "<table cellSpacing=\"0\" class=\"btab\">\n";
        echo "<tr><th>№</th><th>Имя</th><th>Уровень</th><th>Класс</th><th>Статус</th></tr>\n";

        $n = 1;
        foreach($list as $i)
        {
            $cs = $db->get("SELECT * FROM `class_list` WHERE `id` = ".$i['classid']);

            echo "<tr>";
            echo "<td>$n</td>";

			echo "<td><b>{$i['char_name']}&nbsp;</b>";

			if($i['obj_Id'] == $leader_id)
	            echo "&nbsp<img src=\"http://la2.balancer.ru/images/star.gif\" width=\"16\" height=\"16\" valign=\"middle\">";

			if($i['sex'])
            	echo "<img src=\"http://la2.balancer.ru/images/woman.gif\" width=\"11\" height=\"11\" valign=\"middle\">";
			else
            	echo "<img src=\"http://la2.balancer.ru/images/man.gif\" width=\"11\" height=\"11\" valign=\"middle\">";
			echo "</b></td>";

            echo "<td>{$i['level']}</td>";
            list($r, $class) = split("_", $cs['class_name']);
            $race = array('H'=>'Human', 'E'=>'Elven', 'DE'=>'Dark Elven', 'O'=>'Orc', 'D'=>'Dwarven');
            echo "<td>{$race[$r]} $class</td>";
//            echo "<td>$r $class</td>";

			echo "<td>".(($i['online'] > 0*(time()-1000)) ? '<font color="green">online</font>':'&nbsp;')."</td>";

            echo "</tr>\n";
            $n++;
        }

        echo "<tr><th colSpan=\"6\">Всего персонажей: ".sizeof($list)."</th></tr>\n";
        echo "</table>\n";

		echo "<p><a href=\"http://la2.balancer.ru/clans/?\">На страницу кланов &#187;&#187;&#187;</a></p>";
    }

    clan_list($_GET['clan_id']);
?>

