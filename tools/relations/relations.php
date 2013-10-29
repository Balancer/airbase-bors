#!/usr/bin/php
<?php

$_SERVER['DOCUMENT_ROOT'] = '/var/www/balancer.ru/htdocs';

define('BORS_CORE', '/var/www/bors/bors-core');
define('BORS_LOCAL', '/var/www/bors/bors-airbase');
require_once(BORS_CORE.'/init.php');

update();


function update()
{
		// Только степени двоек, точнее — 8*N и 4*N
		$SIZE_2D = 24;
		$SIZE_3D = 4;

		$check_user_ids = array(
//			190,	// killo
//			79,		// aggy
			10000,	// bal
			37,		// varban
//			26052,	// d.vinit
//			5420,	// barbarossa
			14,		// nikita
			843,	// Fakir
			3,		// Aaz
//			91893,	// bugor337
			1037,	// volk959
		);
/*
		$varban = bors_load('balancer_board_user', 37);
		$varban->set_rep_r(0);
		$varban->set_rep_g(9);
		$varban->set_rep_b(0);
		$varban->store();
*/
		$dbf = new driver_mysql('AB_FORUMS');

		if(1)
		{
			$dbf->query("UPDATE users SET
				rep_r = (100*RAND()),
				rep_g = (100*RAND()),
				rep_b = (100*RAND()),
				rep_x = (100*RAND()),
				rep_y = (100*RAND())");
		}

		$check_users = bors_find_all('balancer_board_user', array('id IN' => $check_user_ids, 'by_id' => true));

		foreach($check_users as $u)
			echo "{$u->login()}: {$u->rep_x()}, {$u->rep_y()}\n";

		echo "Загружаем таблицу отношений\n";
		$relations = array();
		$all_user_ids = array();
		$score_min = 9999;
		$score_max= -9999;

		foreach($dbf->select_array('user_relations', '*', array(
			'(score < -5 OR score > 20)',
			'order' => '-score')) as $rel)
		{
			extract($rel);

			if($score > $score_max)
				$score_max = $score;

			if($score < $score_min)
				$score_min = $score;

			if($to_user_id != $from_user_id)
			{
				@$relations[$to_user_id][$from_user_id] += $score;
				@$relations[$from_user_id][$to_user_id] += $score;
			}

			$all_user_ids[] = $from_user_id;
			$all_user_ids[] = $to_user_id;
		}

		$score_diff = $score_max - $score_min;

		$all_user_ids = array_unique($all_user_ids);
		sort($all_user_ids);

		$base_matrix2 = array();
		$base_matrix3 = array();

		$half = $SIZE_2D/2;
		// 22222222222222222222222222222222222222
		$scale = pow($half, 6)/25;

		// Огораживаем края
		for($x=0; $x<$SIZE_2D; $x++)
		{
			for($y=0; $y<$SIZE_2D; $y++)
			{
				$dx = 8*pow(abs($x - $half)/$half, 6);
				$dy = 8*pow(abs($y - $half)/$half, 6);
				@$base_matrix2[$x][$y] += max($dx, $dy);
			}
		}

		// 33333333333333333333333333333333333333
		$half = $SIZE_3D/2;
		$scale = pow($half, 8)/2;
		for($r=0; $r<$SIZE_3D; $r++)
			for($g=0; $g<$SIZE_3D; $g++)
				for($b=0; $b<$SIZE_3D; $b++)
				{
					$dr = 6*pow(abs($r - $half)/$half, 6);
					$dg = 6*pow(abs($g - $half)/$half, 6);
					$db = 6*pow(abs($b - $half)/$half, 6);
					@$base_matrix3[$r][$g][$b] += max($dr, $dg, $db);
				}

//		file_put_contents('matrix2-base0.list', print_r($base_matrix2, true));
//		file_put_contents('matrix3-base0.list', print_r($base_matrix3, true));
		file_put_contents('matrix2-base0.json', json_encode($base_matrix2, JSON_PRETTY_PRINT));
//		file_put_contents('matrix3-base0.json', json_encode($base_matrix3, JSON_PRETTY_PRINT));

		echo "Загружаем всех активных (".count($all_user_ids).") пользователей и создаём базовую матрицу\n";
		$users = array();
		foreach($dbf->get_array("SELECT
				id,
				rep_r*{$SIZE_2D}/100,
				rep_g,
				rep_b,
				rep_x,
				rep_y
			FROM users WHERE id IN (".join(',', $all_user_ids).")") as $u)
		{
			extract($u);

			$users[$id] = $u;

			if($rep_x == 0 || $rep_y == 0)
				continue;

			$step2 = $SIZE_2D/8;
			// Заполняем матрицу отталкивания
			for($dx=-$step2; $dx<=$step2; $dx++)
				for($dy=-$step2; $dy<=$step2; $dy++)
					@$base_matrix2[floor($rep_x+$dx)][floor($rep_y+$dy)] += w2($dx, $dy);

			$step3 = $SIZE_3D/4;
			for($dr=-$step3; $dr<=$step3; $dr++)
				for($dg=-$step3; $dg<=$step3; $dg++)
					for($db=-$step3; $db<=$step3; $db++)
						@$base_matrix3[floor($u['rep_r']+$dr)][floor($u['rep_g']+$dg)][floor($u['rep_b']+$db)] += w3($dr, $dg, $db);
		}

//		file_put_contents('user_ids.list', print_r($all_user_ids, true));
//		file_put_contents('users.list', print_r($users, true));
//		file_put_contents('user-relations.list', print_r($relations, true));
//		file_put_contents('matrix2-base.list', print_r($base_matrix2, true));
//		file_put_contents('matrix3-base.list', print_r($base_matrix3, true));
		file_put_contents('matrix2-base.json', json_encode($base_matrix2, JSON_PRETTY_PRINT));
//		file_put_contents('matrix3-base.json', json_encode($base_matrix3, JSON_PRETTY_PRINT));

		echo "Начинаем цикл по всем пользователям\n";
		$count = 0;
		$voted_user_ids = array_keys($relations);
		sort($voted_user_ids);
//		config_set('mysql_trace_show', true);
		foreach($voted_user_ids as $user_id)
//		$user_id = 10000;
		{
//			t();
			$count++;
			echo '.';

			if(!($user = @$users[$user_id]))
				continue;

			if($count % 10 == 0)
				echo "\r$user_id  $count/".count($voted_user_ids)." ";

			extract($user);

			$matrix2 = $base_matrix2;
			$matrix3 = $base_matrix3;

			$rels = $relations[$user_id];

			$shown = false;

//			echo "\tСчитаем влияние отношений\n";
//			t('beg');
			foreach($voted_user_ids as $voter_id)
			{
				if(!($voter = @$users[$voter_id]))
					continue;

				$score = @$rels[$voter_id];

				if($score < 0)
				{
					// Отталкивание. Падает по мере удаления, по квадрату
					// Чем больше расстояние, тем меньше прибавление
					$score /= 5;
					for($x=0; $x<$SIZE_2D; $x++)
					{
						$dx = $x - $voter['rep_x'];
						$dx2 = $dx*$dx+1;
						for($y=0; $y<$SIZE_2D; $y++)
						{
							$dy = $y - $voter['rep_y'];
							@$matrix2[$x][$y] -= $score/($dx2 + $dy*$dy);
						}
					}

					for($r=0; $r<$SIZE_3D; $r++)
					{
						$dr = $r - $voter['rep_r'];
						$dr2 = $dr*$dr+1;
						for($g=0; $g<$SIZE_3D; $g++)
						{
							$dg = $g - $voter['rep_g'];
							$dg2 = $dr2 + $dg*$dg;
							for($b=0; $b<$SIZE_3D; $b++)
							{
								$db = $b - $voter['rep_b'];
								@$matrix3[$r][$g][$b] -= $score/($dg2 + $db*$db);
							}
						}
					}
				}
				else
				{
					// Притяжение. Растёт по мере удаления, падает по мере приближения
					// Чем больше расстояние, тем больше прибавление
					$score /= 10;
					for($x=0; $x<$SIZE_2D; $x++)
					{
						$dx = $x - $voter['rep_x'];
						$dx2 = $dx*$dx;
						for($y=0; $y<$SIZE_2D; $y++)
						{
							$dy = $y - $voter['rep_y'];
							@$matrix2[$x][$y] += $score*atan(sqrt($dx2 + $dy*$dy)/10);
						}
					}

					for($r=0; $r<$SIZE_3D; $r++)
					{
						$dr = $r - $voter['rep_r'];
						$dr2 = $dr*$dr;
						for($g=0; $g<$SIZE_3D; $g++)
						{
							$dg = $g - $voter['rep_g'];
							$dg2 = $dr2 + $dg*$dg;
							for($b=0; $b<$SIZE_3D; $b++)
							{
								$db = $b - $voter['rep_b'];
								@$matrix3[$r][$g][$b] += $score*atan(sqrt($dg2 + $db*$db)/20);
							}
						}
					}
				}

			}
//			t('rels');

//			echo "Ищем новый оптимум\n";
			echo '?';

			$min2 = 99999;
			$min2r = 99999;
			$min3 = 99999;
			$min3r = 99999;

			for($x=0; $x<$SIZE_2D; $x++)
			{
				for($y=0; $y<$SIZE_2D; $y++)
				{
					$r2 = round(@$matrix2[$x][$y], 2);
					if($r2 < $min2r)
					{
						$min2 = $matrix2[$x][$y];
						$min2r = round($min2, 2);
						$new_rep_x = $x;
						$new_rep_y = $y;
					}
					// Если такой же, но ближе, то — туда
					elseif($r2 == $min2r)
					{
						if(dist2($rep_x - $new_rep_x, $rep_y - $new_rep_y) < dist2($rep_x- $x, $rep_y - $y))
						{
							$new_rep_x = $x;
							$new_rep_y = $y;
						}
					}
				}
			}

			for($r=0; $r<$SIZE_3D; $r++)
			{
				for($g=0; $g<$SIZE_3D; $g++)
				{
					for($b=0; $b<$SIZE_3D; $b++)
					{
						$r3 = round(@$matrix3[$r][$g][$b], 2);
						if($r3 < $min3r)
						{
							$min3 = $matrix3[$r][$g][$b];
							$min3r = round($min3, 2);
							$new_rep_r = $r;
							$new_rep_g = $g;
							$new_rep_b = $b;
						}
						elseif($r3 == $min3r)
						{
							if(dist3($rep_r - $new_rep_r, $rep_g - $new_rep_g, $rep_b - $new_rep_b) < dist3($rep_r- $r, $rep_g - $g, $rep_b - $b))
							{
								$new_rep_r = $r;
								$new_rep_g = $g;
								$new_rep_b = $b;
							}
						}
					}
				}
			}

			// Но перемещаемся к нему только на часть пути
			$new_rep_x = str_replace(',','.',(0.7*$rep_x+0.3*$new_rep_x));
			$new_rep_y = str_replace(',','.',(0.7*$rep_y+0.3*$new_rep_y));
			$new_rep_r = str_replace(',','.',(0.7*$rep_r+0.3*$new_rep_r));
			$new_rep_g = str_replace(',','.',(0.7*$rep_g+0.3*$new_rep_g));
			$new_rep_b = str_replace(',','.',(0.7*$rep_g+0.3*$new_rep_b));

//			t('find');

			if($user_id == 10000)
			{
//				file_put_contents('matrix2-bal.list', print_r($matrix2, true));
//				file_put_contents('matrix3-bal.list', print_r($matrix3, true));
				file_put_contents('matrix2-bal.json', json_encode($matrix2, JSON_PRETTY_PRINT));
//				file_put_contents('matrix3-bal.json', json_encode($matrix3, JSON_PRETTY_PRINT));
				echo "Bal matrix was wrote\n";
			}

			if(in_array($user_id, $check_user_ids))
				file_put_contents('matrix2-'.$user_id.'.json', json_encode($matrix2, JSON_PRETTY_PRINT));

//			if(in_array($user_id, $check_user_ids))
//				echo "\n{$check_users[$user_id]->title()}: ($new_rep_x, $new_rep_y) / $voters_count\n";

			if(in_array($user_id, $check_user_ids))
			{
				echo "\n{$check_users[$user_id]->title()}: ($rep_x, $rep_y) -> ($new_rep_x, $new_rep_y) min2=$min2\n\t"
					."($rep_r, $rep_g, $rep_b) -> ($new_rep_r, $new_rep_g, $new_rep_b), min3=$min3\n";
//				exit();
			}

			$dbf->query("UPDATE users SET rep_r = $new_rep_r, rep_g = $new_rep_g, rep_b = $new_rep_b, rep_x = $new_rep_x, rep_y = $new_rep_y  WHERE id = $user_id");
		}

		$dbf->query("UPDATE users SET rep_r=0, rep_g=0, rep_b=0, rep_x=0, rep_y=0 WHERE id NOT IN (".join(',', $voted_user_ids).')');

		file_put_contents('matrix2-base-end.list', print_r($base_matrix2, true));
		file_put_contents('matrix3-base-end.list', print_r($base_matrix3, true));

		echo "\n";
}

function norm($val, $count = 1)
{
//	$val /= $count;
//	return str_replace(',', '.', $val * exp($val/500));
	return str_replace(',', '.', $val);
}

function sign($x) { return $x < 0 ? -1 : ($x > 0 ? 1 : 0); }
function enforce($x) { return $x*$x*$x; }

function t($section = NULL)
{
	static $time;
	if($section)
		printf("\t$section: %2.4f\n", microtime(true)-$time);
	$time = microtime(true);
}

function w2($dx, $dy) { return 5/(abs($dx) + abs($dy) + 1); }
function w3($dr, $dg, $db) { return 2/(abs($dr) + abs($dg) + abs($db) + 1); }

function dist2($dx, $dy)
{
	return $dx*$dx + $dy*$dy;
}

function dist3($dr, $dg, $db)
{
	return $dr*$dr + $dg*$dg + $db*$db;
}
