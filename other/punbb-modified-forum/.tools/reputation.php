#!/usr/bin/php
<?php
	$_SERVER['DOCUMENT_ROOT'] = '/var/www/balancer.ru/htdocs';

	define('BORS_CORE', '/var/www/.bors/bors-core');
	define('BORS_LOCAL', '/var/www/.bors/bors-airbase');
	require_once(BORS_CORE.'/config.php');
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

		echo "killo: {$killo->rep_x()}, {$killo->rep_y()}\n";
		echo "aggy : {$aggy->rep_x()}, {$aggy->rep_y()}\n";
		echo "bal  : {$bal->rep_x()}, {$bal->rep_y()}\n";
		echo "varba: {$varban->rep_x()}, {$varban->rep_y()}\n";
		echo "dvit : {$dvit->rep_x()}, {$dvit->rep_y()}\n";
		echo "barb : {$barb->rep_x()}, {$barb->rep_y()}\n";
		echo "niki : {$niki->rep_x()}, {$niki->rep_y()}\n\n";
/*
		$bal->set_rep_x(100, true);
		$bal->set_rep_y(100, true);
		$varban->set_rep_x(100, true);
		$varban->set_rep_y(100, true);
		$barb->set_rep_x(-100, true);
		$barb->set_rep_y(-100, true);
		$niki->set_rep_x(-100, true);
		$niki->set_rep_y(-100, true);
		$killo->set_rep_x(100, true);
		$killo->set_rep_y(-100, true);
		$aggy->set_rep_x(-100, true);
		$aggy->set_rep_y(100, true);


//		$killo->set_rep_x(-$aggy->rep_x(), true);
//		$killo->set_rep_y($aggy->rep_y(), true);
//		$aggy->set_rep_x(-$killo->rep_y(), true);
//		$aggy->set_rep_y(-$killo->rep_x(), true);
		$killo->store();
		$aggy->store();
		$bal->store();
		$dvit->store();
		$varban->store();
		$barb->store();
		$niki->store();
*/

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
		$all_reputated_users = array_unique($dbu1->get_array("SELECT DISTINCT user_id FROM `reputation_votes`"));

		foreach($all_reputated_users as $user)
		{
			$total = 0;
			$pure = 0;
//			$srep_r= $srep_g= $srep_b= $srep_x= $srep_y = 0;

			$a = $dbf->get("SELECT reputation, pure_reputation, rep_r, rep_g, rep_b, rep_x, rep_y FROM users WHERE id={$user}");
			list($o_reput, $o_pure_reput, $o_rep_r, $o_rep_g, $o_rep_b, $o_rep_x, $o_rep_y) = array_values($a);

			$srep_x = $o_rep_x;
			$srep_y = $o_rep_y;
			$srep_r = $o_rep_r;
			$srep_g = $o_rep_g;
			$srep_b = $o_rep_b;

//			if($user == 190)
//				echo "=== start srep_x => $srep_x\n";

			foreach($dbu1->get_array("SELECT voter_id as id, SUM(score) as sum FROM `reputation_votes` WHERE user_id = $user AND time GROUP BY voter_id") as $v)
			{
				$a = $dbf->get("SELECT reputation, pure_reputation, rep_r, rep_g, rep_b, rep_x, rep_y FROM users WHERE id={$v['id']}");
				if(!$a)
					continue;

				list($reput, $pure_reput, $rep_r, $rep_g, $rep_b, $rep_x, $rep_y) = array_values($a);

				$drx = $o_rep_x - $rep_x;
				$dry = $o_rep_y - $rep_y;
				$drr = $o_rep_r - $rep_r;
				$drg = $o_rep_g - $rep_g;
				$drb = $o_rep_b - $rep_b;

				$reput = bors_user_reputation_weight($reput);
				$pure_reput = bors_user_reputation_weight($pure_reput);
//				if($v['id'] == 10000) echo "$rep_x -> ";
				$rep_r = bors_user_reputation_weight_signed($rep_r);
				$rep_g = bors_user_reputation_weight_signed($rep_g);
				$rep_b = bors_user_reputation_weight_signed($rep_b);
				$rep_x = bors_user_reputation_weight_signed($rep_x);
				$rep_y = bors_user_reputation_weight_signed($rep_y);
				$group = $dbf->get("SELECT group_id FROM users WHERE id={$v['id']}");

/*				$div = (abs($rep_r+$rep_g+$rep_b)+0.01)/3;
				$rep_r/=$div;
				$rep_g/=$div;
				$rep_b/=$div;

				$div = (abs($rep_y+$rep_x)+0.01)/2;
				$rep_x/=$div;
				$rep_y/=$div;
*/
//				if($v['id'] == 10000) echo "$rep_x\n";

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

//				if($sco < 0)
//					$sco /= 2;

				foreach(explode(' ', 'x y r g b') as $i)
				{
					$rd = 1-${"dr$i"}/${"td$i"};

					if($sco > 0)
					{
						$attr = 2 * $sco * ${"rep_$i"} / ($rd * $rd + 0.1);
						$repuls = 101*$attr/(${"dr$i"}*${"dr$i"}+1);
					}
					else
					{
						$attr = ${"td$i"} * $sco * ${"rep_$i"}/(${"dr$i"}*${"dr$i"}+1);
						$repuls = ${"td$i"} / 10 * (1-$sco) * ${"rep_$i"}/(abs(${"dr$i"})+1);
					}

//					$attr   = $sco * ${"rep_$i"} / (${"dr$i"}/${"td$i"}+0.1) / 10 /* + rand()/getrandmax() - 0.5 */;
//					$repuls = 26*$attr/(${"dr$i"}*${"dr$i"}+1);

					${"srep_$i"} -= ($attr - $repuls);

//					if($i == 'x' && $user == 190/* && (abs($attr) > 1 || abs($repuls) > 1)*/)
//						echo "    attr   = $attr\n    repuls = $repuls\n";

				}
/*
				$srep_r -=  $sco * $rep_x * $drx / $tdx;
				$srep_g -=  $sco * $rep_y * $dry / $tdy;
				$srep_b -=  $sco * $rep_r * $drr / $tdr;
				$srep_x -=  $sco * $rep_g * $drg / $tdg;
				$srep_y -=  $sco * $rep_b * $drb / $tdb;
*/
//				echo "$user <- {$v['id']}: s={$v['sum']}, t={$v['total']}, r=$reput, w=$weight  --> $total/$pure\n";
			}

			$totaln = atan($total)*2/pi();

			foreach(explode(' ', 'x y r g b') as $i)
				${"srep_$i"} = (0.5*${"td$i"}+20) * bors_user_reputation_weight_signed(${"srep_$i"}/5);

			foreach(explode(' ', 'total pure srep_r srep_g srep_b srep_x srep_y') as $var)
				$$var = str_replace(',', '.', $$var);

			if($user == 34059)
				echo "pure = $pure, total=$total, srep_x => $srep_x\n";

			$dbf->query("UPDATE users SET 
					reputation = $total,
					pure_reputation = $pure,
					rep_r = $srep_r,
					rep_g = $srep_g,
					rep_b = $srep_b,
					rep_x = $srep_x,
					rep_y = $srep_y
				 WHERE id = $user");
//			$dbf->query("UPDATE users SET pure_reputation = $pure WHERE id = $user");
//			echo $dbf->get("SELECT username FROM users WHERE id=$user")."[$user]: $total ($totaln)\n";
		}

		$dbf->query("UPDATE users SET pure_reputation = 0, reputation = 0 WHERE id NOT IN (".join(',', $all_reputated_users).')');
		$dbf->query("UPDATE users SET rep_r=0, rep_g=0, rep_b=0, rep_x=0, rep_y=0 WHERE id NOT IN (".join(',', $all_reputated_users).')');
	}
