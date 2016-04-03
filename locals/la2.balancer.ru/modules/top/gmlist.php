<?php
    function modules_top_gmlist_main($title, $db)
    {
		$ch = new bors_cache();
		if($ch->get("la2", "gmlist2-$db"))
			return $ch->last;

		$res = "";

        include_once('funcs/DataBase.php');
        $hts = new driver_mysql($db,'la2', 'la2kkk');
        $list = $hts->get_array("
			SELECT * 
			FROM `characters` 
				LEFT JOIN character_subclasses ON obj_Id = char_obj_id
			WHERE `accesslevel` >= 30 
			ORDER BY `char_name`;");
		
        $res .= "<table cellSpacing=\"0\" class=\"btab\" width=\"100%\">\n";
		$res .= "<tr><th colSpan=\"6\">$title</th></tr>\n";
        $res .= "<tr><th>№</th><th>Имя пользователя</th><th>Уровень доступа GM</th><th>Уровень в игре</th><th>Пол</th><th>Класс</th></tr>\n";

        $n = 1;
        foreach($list as $i)
        {
//            $GLOBALS['log_level'] = 9;
            $cs = $hts->get("SELECT * FROM `class_list` WHERE `id` = ".$i['class_id']);
//            $GLOBALS['log_level'] = 2;

            $res .= "<tr>";
            $res .= "<td>$n</td>";
            $res .= "<td><b>{$i['char_name']}</b></td>";
            $res .= "<td><b>{$i['accesslevel']}</b></td>";
            $res .= "<td>{$i['level']}</td>";
            $sex = array(0=>'М', 1=>'Ж');
            $res .= "<td>{$sex[$i['sex']]}</td>";
            list($r, $class) = split("_", $cs['class_name']);
            $race = array('H'=>'Human', 'E'=>'Elven', 'DE'=>'Dark Elven', 'O'=>'Orc', 'D'=>'Dwarven');
            $res .= "<td>{$race[$r]} $class</td>";
//            $res .= "<td>$r $class</td>";

            $res .= "</tr>\n";
            $n++;
        }

        $res .= "</table>\n";

		return $ch->set($res, 86400);
    }

    echo modules_top_gmlist_main("Основной", "l2jdb");
    echo modules_top_gmlist_main("Тестовый", "l2jtestdb");
?>

<ul>
<li>Данные обновляются один раз в сутки.</li>
</ul>
