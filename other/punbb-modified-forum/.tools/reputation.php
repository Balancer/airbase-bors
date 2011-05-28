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
			);

		$killo = object_load('bors_user', 190);
		$aggy = object_load('bors_user', 79);
		$bal = object_load('bors_user', 10000);
		$varban = object_load('bors_user', 37);
		$dvit = object_load('bors_user', 26052);
		$barb = object_load('bors_user', 5420);
		$niki = object_load('bors_user', 14);

//		$killo->set_rep_x(3, true);
//		$killo->set_rep_y(-10, true);
//		$killo->store();

		echo "killo: {$killo->rep_x()}, {$killo->rep_y()} [{$killo->reputation()}, {$killo->pure_reputation()}]\n";
		echo "aggy : {$aggy->rep_x()}, {$aggy->rep_y()}\n";
		echo "bal  : {$bal->rep_x()}, {$bal->rep_y()}\n";
		echo "varba: {$varban->rep_x()}, {$varban->rep_y()}\n";
		echo "dvit : {$dvit->rep_x()}, {$dvit->rep_y()}\n";
		echo "barb : {$barb->rep_x()}, {$barb->rep_y()}\n";
		echo "niki : {$niki->rep_x()}, {$niki->rep_y()}\n\n";

		$dbf = new driver_mysql('punbb');
		$dbu1 = new driver_mysql('USERS');
		$dbu2 = new driver_mysql('USERS');

		extract($dbf->select('users', '
			MIN(rep_x) as min_x, 
			MAX(rep_x) as max_x, 
			MIN(rep_y) as min_y, 
			MAX(rep_y) as max_y, 
			MIN(rep_r) as min_r, 
			MAX(rep_r) as max_r, 
			MIN(rep_g) as min_g, 
			MAX(rep_g) as max_g, 
			MIN(rep_b) as min_b, 
			MAX(rep_b) as max_b
		', array('order'=>'last_post DESC', 'limit' => 150)));

		$tdx = $max_x - $min_x +1;
		$tdy = $max_y - $min_y +1;

		$tdr = $max_r - $min_r +1;
		$tdg = $max_g - $min_g +1;
		$tdb = $max_b - $min_b +1;

		echo "tdx: $max_x - $min_x = $tdx, tdy: $max_y - $min_y = $tdy\n";
		$all_reputated_user_ids = array_unique($dbu1->get_array("SELECT DISTINCT user_id FROM `reputation_votes`"));

		$rnames = explode(' ', 'x y r g b');
		$cache = array();

		// Большой цикл по всем юзерам
		foreach($dbf->get_array("SELECT 
				id as user_id,
				reputation as o_reput,
				pure_reputation as o_pure_reput,
				rep_r as o_rep_r,
				rep_g as o_rep_g,
				rep_b as o_rep_b,
				rep_x as o_rep_x,
				rep_y  as o_rep_y
			FROM users WHERE id IN (".join(',', $all_reputated_user_ids).")") as $x)
		{
			$total = 0;
			$pure = 0;

			extract($x);

			foreach($rnames as $i)
				${"srep_$i"} = ${"o_rep_$i"};

			// Считаем изменение координат текущего юзера.
			// Цикл по всем, кто по нему высказывался:
			$voters = $dbu1->get_array("SELECT voter_id as id, SUM(score) as sum FROM `reputation_votes` WHERE user_id = $user_id AND time GROUP BY voter_id");
			$voters_count = count($voters);
			foreach($voters as $v)
			{
				$voter_id = $v['id'];
				// Извлекаем данные по высказывавшемуся $v:
				if(array_key_exists($voter_id, $cache))
					$a = $cache[$voter_id];
				else
					$cache[$voter_id] = $a = $dbf->get("SELECT reputation, pure_reputation, rep_r, rep_g, rep_b, rep_x, rep_y FROM users WHERE id={$v['id']}");

				if(!$a)
					continue;

				list($reput, $pure_reput, $rep_r, $rep_g, $rep_b, $rep_x, $rep_y) = array_values($a);

				// Это разница репутация нашего базового пользователя и сравниваемого
				foreach($rnames as $i)
					${"dr$i"} = ${"o_rep_$i"} - ${"rep_$i"};

				$l2 = sqrt($drx*$drx + $dry*$dry) + .01;
				$l3 = sqrt($drr*$drr + $drg*$drg + $drb*$drb) + .01;

				$scores = atan($v['sum'])*2/pi();
				$scores2 = sqrt($scores * $scores);
				$need = 50 - ($scores+1)*20; // -1 -> 50, 0 -> 30, +1 ->10

				$srep_x += ($scores2 * $drx * ($need/$l2 - 1))/$voters_count;
				$srep_y += ($scores2 * $dry * ($need/$l2 - 1))/$voters_count;
				$srep_r += ($scores2 * $drr * ($need/$l3 - 1))/$voters_count;
				$srep_g += ($scores2 * $drg * ($need/$l3 - 1))/$voters_count;
				$srep_b += ($scores2 * $drb * ($need/$l3 - 1))/$voters_count;

				$reput = bors_user_reputation_weight($reput);
				$pure_reput = bors_user_reputation_weight($pure_reput);

				$group = $dbf->get("SELECT group_id FROM users WHERE id={$v['id']}");

				$weight = @$grw[$group];
				if(!$weight)
					$weight = 1;

				if($v['id'] == 10000)
					$weight = 9;

				if($dbf->get("SELECT num_posts FROM users WHERE id={$v['id']}") < 50)
					$weight = 0;

				$total += atan($v['sum'])*2/pi() * $weight * $reput;
				$pure  += atan($v['sum'])*2/pi() * $pure_reput;

				$sco = atan($v['sum'])*2/pi();
			}

			$totaln = atan($total)*2/pi();

			foreach(explode(' ', 'total pure') as $var)
				$$var = str_replace(',', '.', $$var);

			foreach(explode(' ', 'srep_r srep_g srep_b srep_x srep_y') as $var)
			{
				$$var -= $$var*$$var*$$var/1e6;

				if($$var > 100)
					$$var = 100;
				if($$var < -100)
					$$var = -100;

				$$var = str_replace(',', '.', $$var);
			}

			if($user_id == 34059)
				echo "pure = $pure, total=$total, srep_x => $srep_x\n";

			$dbf->query("UPDATE users SET 
					reputation = $total,
					pure_reputation = $pure,
					rep_r = $srep_r,
					rep_g = $srep_g,
					rep_b = $srep_b,
					rep_x = $srep_x,
					rep_y = $srep_y
				 WHERE id = $user_id");
		}

		$dbf->query("UPDATE users SET pure_reputation = 0, reputation = 0 WHERE id NOT IN (".join(',', $all_reputated_user_ids).')');
		$dbf->query("UPDATE users SET rep_r=0, rep_g=0, rep_b=0, rep_x=0, rep_y=0 WHERE id NOT IN (".join(',', $all_reputated_user_ids).')');
	}

function sq($x) { return $x*$x; }
function sqp($x, $diff) { return $x*$x + $diff; }
function sign($x) { return $x > 0 ? 1 : ($x < 0 ? -1 : 0); }
