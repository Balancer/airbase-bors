#!/usr/bin/php
<?php
	$_SERVER['DOCUMENT_ROOT'] = '/var/www/balancer.ru/htdocs';

	define('BORS_CORE', '/var/www/bors/bors-core');
	define('BORS_LOCAL', '/var/www/bors/bors-airbase');
	require_once(BORS_CORE.'/init.php');
	include_once('inc/users.php');

	update();

	function update()
	{

		$grw = array(
				1 => 7, // admin
				2 => 5, // moder
				3 => 0, // guest
				5 => 3, // coordin
				6 => 2, // старожилы
				21 => 3, // координатор-литератор
				26 => 0, // пария
			);

		$dbf = new driver_mysql(config('punbb.database'));
		$dbu1 = new driver_mysql('USERS');

		$all_reputated_user_ids = array_unique($dbu1->get_array("SELECT DISTINCT user_id FROM `reputation_votes`"));

		$cache = array();

		// Большой цикл по всем юзерам
		foreach($dbf->get_array("SELECT 
				id as user_id,
				reputation as o_reput,
				pure_reputation as o_pure_reput
			FROM users WHERE id IN (".join(',', $all_reputated_user_ids).")") as $x)
		{
			$total = 0;
			$pure = 0;

			extract($x);

			// Считаем изменение координат текущего юзера.
			// Цикл по всем, кто по нему высказывался:
			$voters = $dbu1->get_array("SELECT voter_id as id, SUM(score) as sum
				FROM `reputation_votes`
				WHERE user_id = $user_id
					AND time
					AND is_deleted=0
				GROUP BY voter_id");
			$voters_count = count($voters);
			foreach($voters as $v)
			{
				$voter_id = $v['id'];
				// Извлекаем данные по высказывавшемуся $v:
				if(array_key_exists($voter_id, $cache))
					$a = $cache[$voter_id];
				else
					$cache[$voter_id] = $a = $dbf->get("SELECT reputation, pure_reputation FROM users WHERE id={$v['id']}");

				if(!$a)
					continue;

				list($reput, $pure_reput) = array_values($a);

				$scores = atan($v['sum'])*2/pi();
				$scores2 = sqrt($scores * $scores);
				$need = 50 - ($scores+1)*20; // -1 -> 50, 0 -> 30, +1 ->10

				$reput = bors_user_reputation_weight($reput);
				$pure_reput = bors_user_reputation_weight($pure_reput);

				$group = $dbf->get("SELECT group_id FROM users WHERE id={$v['id']}");

				$weight = @$grw[$group];
				if(!$weight)
					$weight = 1;

				if($v['id'] == 10000)
					$weight = 10;

				if($dbf->get("SELECT num_posts FROM users WHERE id={$v['id']}") < 50)
					$weight = 0;

				$total += atan($v['sum'])*2/pi() * $weight * $reput;
				$pure  += atan($v['sum'])*2/pi() * $pure_reput;

				$sco = atan($v['sum'])*2/pi();
			}

			$totaln = atan($total)*2/pi();

			foreach(explode(' ', 'total pure') as $var)
				$$var = str_replace(',', '.', $$var);

			$dbf->query("UPDATE users SET
					reputation = $total,
					pure_reputation = $pure
				 WHERE id = $user_id");
		}

		$dbf->query("UPDATE users SET pure_reputation = 0, reputation = 0 WHERE id NOT IN (".join(',', $all_reputated_user_ids).')');
	}

function sq($x) { return $x*$x; }
function sqp($x, $diff) { return $x*$x + $diff; }
function sign($x) { return $x > 0 ? 1 : ($x < 0 ? -1 : 0); }
