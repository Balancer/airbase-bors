#!/usr/bin/php
<?
	$_SERVER['DOCUMENT_ROOT'] = "/var/www/balancer.ru/htdocs";

	update();

	function update()
	{
		include("{$_SERVER['DOCUMENT_ROOT']}/cms/config.php");
		include_once("funcs/DataBase.php");
	
		$dbf = new DataBase('punbb');
		$dbu1 = new DataBase('USERS');
		$dbu2 = new DataBase('USERS');
		
		$grw = array(
				1 => 8, // admin
				2 => 6, // moder
				3 => 0, // guest
				5 => 4, // coordin
				6 => 2, // старожилы
			);

		foreach($dbu1->get_array("SELECT DISTINCT user_id FROM `reputation_votes`") as $user)
		{
			$total = 0;
			foreach($dbu1->get_array("SELECT voter_id as id, SUM(score) as sum FROM `reputation_votes` WHERE user_id = $user GROUP BY voter_id") as $v)
			{
				$reput = (atan($dbf->get("SELECT reputation FROM users WHERE id={$v['id']}"))*2/pi() + 1)/2;
				$group = $dbf->get("SELECT group_id FROM users WHERE id={$v['id']}");
				
				$weight = $grw[$group];
				if(!$weight)
					$weight = 1;

				if($v['id'] == 10000)
					$weight = 10;
					
				if($dbf->get("SELECT num_posts FROM users WHERE id={$v['id']}") < 50)
					$weight = 0;
				
				$sum = atan($v['sum'])*2/pi() * $weight * $reput;
				$total += $sum;
//				echo "$user <- {$v['id']}: s={$v['sum']}, t={$v['total']}, r=$reput, w=$weight  --> $sum\n";
			}

			$totaln = atan($total)*2/pi();
			
			$dbf->query("UPDATE users SET reputation = $total WHERE id = $user");
			echo $dbf->get("SELECT username FROM users WHERE id=$user")."[$user]: $total ($totaln)\n";
		}
	}
