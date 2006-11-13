<?
    register_handler("(\d+)/reputation_line/(.*)", 'balancer_plugins_user_reputation_line_handler');

    function balancer_plugins_user_reputation_line_handler($uri, $m)
	{
		$user_id = intval($m[1]);
		if(!$user_id)
			return false;

		$ch = &new Cache();
		if($ch->get('reputation-line-2', $user_id))
		{
			$line = $ch->last();
		}
		else
		{
			$db = &new DataBase('punbb');
		
			$reputation_value = $db->get("SELECT reputation FROM users WHERE id = $user_id");
		
			$reputation_abs = intval(0.9 + 20*atan(abs($reputation_value)/4)/pi())/2;
		
			$reputation = "";
			if($reputation_abs)
			{
				$reputation .= str_repeat("<img src=\"http://balancer.ru/img/web/".($reputation_value > 0 ? "star" : "bstar").".gif\" width=\"16\" height=\"16\" border=\"0\">", intval($reputation_abs));

				if($reputation_abs != intval($reputation_abs))
				{
					$reputation .= "<img src=\"http://balancer.ru/img/web/".($reputation_value > 0 ? "star" : "bstar")."-half.gif\" width=\"16\" height=\"16\" border=\"0\">";
					$reputation_abs += 0.5;
				}
			
				if($reputation_abs < 5)
					$reputation .= str_repeat("<img src=\"http://balancer.ru/coppermine/images/flags/blank.gif\" width=\"16\" height=\"16\" border=\"0\">", 5-$reputation_abs);
			}

			$dbu = &new DataBase('USERS');
			$plus  = $dbu->get("SELECT COUNT(*) FROM reputation_votes WHERE user_id = $user_id AND score > 0");
			$minus = $dbu->get("SELECT COUNT(*) FROM reputation_votes WHERE user_id = $user_id AND score < 0");

			$line = $ch->set("<a href=\"http://balancer.ru/user/$user_id/reputation/%ref%\" title=\"Репутация: ".sprintf("%.1f", $reputation_value)." [+$plus / -$minus]. \nНажмите для подробного просмотра.\"	class=\"hoverable_link\">$reputation</a>", 3600);
		}

		$ref=@$m[2];

		echo str_replace("%ref%", $ref, $line);
		return true;
	}
