<?
    function modules_top_pk_main()
    {
		$ch = new Cache();
		if($ch->get("la2", "top-pk"))
			return $ch->last;

		$res = "";
	
        include_once('funcs/DataBase.php');
        $hts = new DataBase('l2jdb','la2', 'la2kkk');

        $list = $hts->get_array("
			select * 
			from characters 
				LET JOIN character_subclasses on obj_Id = char_obj_id
			where pkkills>0 order by pkkills desc, level;", false);

        $res .= "<table cellSpacing=\"0\" class=\"btab\" width=\"100%\">\n";
        $res .= "<tr><th>№</th><th>Имя</th><th>Уровень</th><th>ПК</th><th>Карма</th></tr>\n";

        $n = 1;
        foreach($list as $i)
        {
			if($n>30)
				continue;
				
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
            $res .= "<td>$n</td>";
            $res .= "<th>$bc{$i['char_name']}&nbsp;$ec";
			
			if($i['sex'])
            	$res .= "<img src=\"http://la2.balancer.ru/images/woman.gif\" width=\"11\" height=\"11\" valign=\"middle\">";
			else
            	$res .= "<img src=\"http://la2.balancer.ru/images/man.gif\" width=\"11\" height=\"11\" valign=\"middle\">";
			$res .= "</th>";
			
            $res .= "<td>{$i['level']}".($i['accesslevel']>10?" <span style=\"7pt;\">(GM)</span>":"")."</td>";
            $res .= "<td>{$i['pkkills']}</td>";
            $res .= "<td>{$bc}{$i['karma']}{$ec}</td>";

            $res .= "</tr>\n";
            $n++;
        }

        $res .= "<tr><th colSpan=\"5\">Всего ПК: ".sizeof($list)."</th></tr>\n";
        $res .= "</table>\n";

		return $ch->set($res, 3600);
    }

    echo modules_top_pk_main();
?>
<ul>
<li>Данные обновляются один раз в час.</li>
</ul>
