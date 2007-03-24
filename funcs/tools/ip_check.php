<?
	function agava_ip_check()
	{
		if(!preg_match("!^(\d+)\.(\d+)\.(\d+)\.(\d+)$!", $_SERVER["REMOTE_ADDR"], $m))
			exit("Извините, неопознанный формат Вашего IP!");

		$uip = $m[1]<<24 | $m[2]<<16 | $m[3]<<8 | $m[4];
	
		foreach(file("/var/www/files.balancer.ru/htdocs/runet2.txt") as $ip)
		{
			if(preg_match("!^(\d+)\.(\d+)\.(\d+)\.(\d+)/(\d+)$!", $ip, $m))
			{
				$tip = $m[1]<<24 | $m[2]<<16 | $m[3]<<8 | $m[4];
				$mask = -1 << (32-$m[5]);

				if(($tip & $mask) == ($uip & $mask))
					return $ip;
			}
		}

		exit("Извините, но вашей подсети {$_SERVER['REMOTE_ADDR']} (".sprintf("%x",$uip).") нет в списке русских подсетей провайдера Agava.Ru. Вы можете воспользоваться одним из прокси, входящих в подсети, перечисленные в списке <a href=\"http://agava.ru/runet2\">http://agava.ru/runet2</a>. Такое ограничение связано с серьёзной переплатой по зарубежному трафику.");
	}

	function is_foreign_network()
	{
		if(!preg_match("!^(\d+)\.(\d+)\.(\d+)\.(\d+)$!", $_SERVER["REMOTE_ADDR"], $m))
			return ec("Извините, неопознанный формат Вашего IP!");

		$uip = $m[1]<<24 | $m[2]<<16 | $m[3]<<8 | $m[4];
	
		foreach(file("/var/www/files.balancer.ru/htdocs/runet.txt") as $ip)
		{
			if(preg_match("!^(\d+)\.(\d+)\.(\d+)\.(\d+)/(\d+)$!", $ip, $m))
			{
				$tip = $m[1]<<24 | $m[2]<<16 | $m[3]<<8 | $m[4];
				$mask = -1 << (32-$m[5]);

				if(($tip & $mask) == ($uip & $mask))
					return false;
			}
		}

		return ec("Извините, но вашей подсети {$_SERVER['REMOTE_ADDR']} (".sprintf("%x",$uip).") нет в списке русских подсетей провайдера Agava.Ru. Вы можете воспользоваться одним из прокси, входящих в подсети, перечисленные в списке <a href=\"http://agava.ru/runet2\">http://agava.ru/runet2</a>. Такое ограничение связано с серьёзной переплатой по зарубежному трафику.");
	}
