<?
    function modules_top_max_main()
    {
        include_once('funcs/DataBase.php');
        $hts = new driver_mysql('l2jdb','la2', 'la2kkk');
        $max1 = $hts->get("select * from online order by count desc, date asc limit 1;");
        $max2 = $hts->get("select * from online order by count desc, date desc limit 1;");

		$max = $max2['count'];

		echo "<h3>Зарегистрированный максимум: $max</h3>";
        echo "<table cellSpacing=\"0\" class=\"btab\" width=\"100%\">\n";
        echo "<tr><th>Первое наблюдение</th><th>Последнее наблюдение</th></tr>\n";
		echo "<tr><td>".strftime("%Y-%m-%d %H:%M", $max1['date'])."</td><td>".strftime("%Y-%m-%d %H:%M", $max2['date'])."</td></tr>\n";
        echo "</table>\n";
    }

    modules_top_max_main();
?>

