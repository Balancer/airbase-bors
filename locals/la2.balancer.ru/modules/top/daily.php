<?php
    function modules_top_daily_main($days)
    {
        include_once('funcs/DataBase.php');
        include_once('funcs/Cache.php');
		
		$ch = new bors_cache();

		if($ch->get('l2j-top-daily', $days))
		{
			echo $ch->last();
			return;
		}
		
        $db = new DataBase('l2jdb');
        $max = $db->get("select max(count) from online;");

		$d = array();
		$m = array();
		$c = array();

        foreach($db->get_array("select * from online where date > ".(time()-$days*86400)." order by date desc") as $r)
		{
			$dd = strftime("%Y-%m-%d, %a",$r['date']);
			@$d[$dd] += $r['count'];
			@$c[$dd] ++;
			if($r['count'] > @$m[$dd])
				$m[$dd] = $r['count'];
		}

		$ret = "<h3>Средний онлайн по дням</h3>";
        $ret .= "<table cellSpacing=\"0\" class=\"btab\" width=\"100%\">\n";
        $ret .= "<tr><th>День</th><th>Число игроков среднее/максимальное</th></tr>\n";
		
		krsort($d);
		
		foreach($d as $day => $cnt)
		{
			$avg = $cnt/$c[$day];
			$max_draw = intval($m[$day]*100/$max+0.5);
			$avg_draw = intval($avg*100/$max+0.5);
			$ret .= "<tr><td><span style=\"font-size:6pt;\">$day</span></td><td><span style=\"font-size:6pt;\"><span style=\"background-color: #A1A6C0;\">".str_repeat('&nbsp;', $avg_draw)."</span><span style=\"background-color: #8993b4;\">".str_repeat('&nbsp;', $max_draw-$avg_draw)."</span> ".intval($avg+0.5)."/{$m[$day]}</span></td></tr>\n";
		}
        $ret .= "</table>\n";

		echo $ch->set($ret, 86400);
    }

    modules_top_daily_main(150);
