<?
    function modules_top_count_main()
    {
        $hts = new DataBase('l2jdb','la2', 'la2kkk');
        $max = $hts->get("select max(count) from online;");

		$h = array();
		$m = array();
		$c = array();
        foreach($hts->get_array("select * from online where date > ".(time()-14*86400)) as $r)
		{
			$hh = intval(strftime("%H",$r['date']));
			@$h[$hh] += $r['count'];
			@$c[$hh] ++;
			if($r['count'] > @$m[$hh])
				$m[$hh] = $r['count'];
		}
		echo "<h3>Средний онлайн по часам</h3>";
        echo "<table cellSpacing=\"0\" class=\"btab\" width=\"100%\">\n";
        echo "<tr><th>Время</th><th>Число игроков среднее/максимальное</th></tr>\n";
		
		for($i=0; $i<24; $i++)
		{
			$avg = $h[$i]/$c[$i];
			$max_draw = intval($m[$i]*50/$max+0.5);
			$avg_draw = intval($avg*50/$max+0.5);
			echo "<tr><td>$i:00 .. $i:59</td><td><span style=\"background-color: #A1A6C0;\">".str_repeat('&nbsp;', $avg_draw)."</span><span style=\"background-color: #8993b4;\">".str_repeat('&nbsp;', $max_draw-$avg_draw)."</span> ".intval($avg+0.5)."/{$m[$i]}</td></tr>\n";
		}
        echo "</table>\n";

    }

    modules_top_count_main();
?>

