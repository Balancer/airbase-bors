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
		//TODO: сделать как-нибудь по-иному!
		return false;
		$msg = ec("Извините, но вашей подсети {$_SERVER['REMOTE_ADDR']} нет в списке русских подсетей провайдера Agava.Ru. Вы можете воспользоваться одним из прокси, входящих в подсети, перечисленные в списке <a href=\"http://agava.ru/runet2\">http://agava.ru/runet2</a>. Такое ограничение связано с серьёзной переплатой по зарубежному трафику.");

		if(!empty($_COOKIE['bors_ifn']))
			return $_COOKIE['bors_ifn'] == 2 ? $msg : false;

		$cache = new Cache();
		if($cache->get('is_foreign_network', $_SERVER['REMOTE_ADDR']))
			return $cache->last() == 2 ? $msg : false;
	
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
				{
					SetCookie('bors_ifn', 1, time() + 86400, "/", $_SERVER['HTTP_HOST']);
					$cache->set(1, -86400);
					return false;
				}
			}
		}

		SetCookie('bors_ifn', 2, time() + 86400, "/", $_SERVER['HTTP_HOST']);
		$cache->set(2, -86400);
		return ec("Извините, но вашей подсети {$_SERVER['REMOTE_ADDR']} (".sprintf("%x",$uip).") нет в списке русских подсетей провайдера Agava.Ru. Вы можете воспользоваться одним из прокси, входящих в подсети, перечисленные в списке <a href=\"http://agava.ru/runet2\">http://agava.ru/runet2</a>. Такое ограничение связано с серьёзной переплатой по зарубежному трафику.");
	}
