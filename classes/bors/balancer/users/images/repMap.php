<?php

require_once 'Image/Canvas.php';

class balancer_users_images_repMap extends bors_image_svg
{
	function show_image()
	{

		$dbh = new driver_mysql('AB_FORUMS');
		$dbh->query('CREATE TEMPORARY TABLE __minmaxrep SELECT rep_x, rep_y, rep_r, rep_g, rep_b FROM users WHERE rep_x<>0');
		extract($dbh->get('SELECT
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
		FROM __minmaxrep'));

		$this->min_x = $min_x = -100;
		$this->max_x = $max_x = 100;
		$this->min_y = $min_y = -100;
		$this->max_y = $max_y = 100;

		$min_r = -100;
		$min_g = -100;
		$min_b = -100;
		$max_r = 100;
		$max_g = 100;
		$max_b = 100;

		$dx = $max_x - $min_x;
		$dy = $max_y - $min_y;

		$dr = $max_r - $min_r;
		$dg = $max_g - $min_g;
		$db = $max_b - $min_b;

		$relations = array();
		$users = array();
		foreach($dbh->select_array('user_relations', '*', array(
//			'votes_plus + votes_minus + reputations_plus + reputations_minus > 30',
//			'(score < -20 OR score > 50)',
			'order' => '-score',
		)) as $rel)
		{
			$score = $rel['score'];
			$user_id = $rel['to_user_id'];
			$voter_id = $rel['from_user_id'];
			if($user_id != $voter_id && count(@$relations[$user_id]) < 2)
				$relations[$user_id][$voter_id] = $score;

			if(empty($users[$user_id]))
				$users[$user_id] = $user_id;

			if(empty($users[$voter_id]))
				$users[$voter_id] = $voter_id;
		}

		$users = bors_find_all('balancer_board_user', array('id IN' => array_keys($users), 'by_id' => true));

		$this->offset = $offset = 30;;

		$width = 1000;
		$height = $width*$dy/$dx;

		$this->scale = $scale = ($width - 60) / $dx;

		// change the output format with the first parameter of factory()
		$Canvas =& Image_Canvas::factory('svg', array(
			'width' => $width,
			'height' => round($height),
			'encoding' => 'utf-8'
		));

		$Canvas->setLineColor('black');
		$Canvas->rectangle(array('x0' => $offset/2, 'y0' => $offset/2, 'x1' => $width-$offset/2, 'y1' => $height-$offset/2));

		$Canvas->setLineColor('black');
		$Canvas->setLineThickness(.1);
		$Canvas->line(array('x0' => $this->x(0), 'y0' => $this->y($min_y), 'x1' => $this->x(0), 'y1' => $this->y($max_y)));
		$Canvas->setLineColor('black');
		$Canvas->setLineThickness(.1);
		$Canvas->line(array('x0' => $this->x($min_x), 'y0' => $this->y(0), 'x1' => $this->x($max_x), 'y1' => $this->y(0)));

//		$Canvas->addText(array('x' => $this->x($min_x), 'y' => $this->y($min_y), 'text' => "x=[{$min_x}..{$max_x}]\ny=[{$min_y}..{$max_y}]"));

//		foreach($dbh->select_array('users', '*', array('rep_x<>0', 'order'=>'last_post DESC', 'limit' => 150)) as $r)
		foreach($dbh->select_array('users', '*', array('id IN' => array_keys($users), 'rep_x<>0', 'rep_y<>0')) as $r)
		{
			$user_id = $r['id'];

			$x = ($r['rep_x']-$min_x)*$scale+$offset;
			$y = ($r['rep_y']-$min_y)*$scale+$offset;

			$cr = $this->sq(($r['rep_r']-$min_r)/$dr)*255;
			$cg = $this->sq(($r['rep_g']-$min_g)/$dg)*255;
			$cb = $this->sq(($r['rep_b']-$min_b)/$db)*255;

			$Canvas->setGradientFill(array(
				'direction' => 'horizontal', 
				'start' => sprintf('#%02x%02x%02x', $cr, $cg, $cb),
				'end' => sprintf('#%02x%02x%02x', $cr, $cg, $cb),
			));

			$Canvas->ellipse(array('x' => $x, 'y' => $y, 'rx' => 3, 'ry' => 3));
			$Canvas->setFont(array('name' => 'Verdana', 'size' => 10));
			$Canvas->addText(array('x' => $x, 'y' => $y, 'text' => $r['username']/*.'='.$r['rep_x']*/, 'color'=>sprintf('#%02x%02x%02x', $cr, $cg, $cb)));

			if(($rel = @$relations[$user_id]))
			{
				foreach($rel as $voter_id => $score)
				{
					if($score > 0 and $score < 20)
						continue;

					if($score < 0 and $score > -25)
						continue;

					$dx = $r['rep_x'] - $users[$voter_id]->rep_x();
					$dy = $r['rep_y'] - $users[$voter_id]->rep_y();
//					if($dx*$dx + $dy*$dy > abs($score*2))
//						continue;

					$strong = min(255, 255*abs($score / ($score>0?150:150)));
					$green = sprintf("#%02x%02x%02x", 255-$strong, 255, 255-$strong);
					$red   = sprintf("#%02x%02x%02x", 255, 255-$strong, 255-$strong);

					$Canvas->setLineColor($score > 0 ? $green : $red);
					$Canvas->setLineThickness(.001);
					$Canvas->line(array(
						'x0' => $x,
						'y0' => $y,
						'x1' => ($users[$voter_id]->rep_x() - $min_x) * $scale + $offset,
						'y1' => ($users[$voter_id]->rep_y() - $min_y) * $scale + $offset
					));
				}
			}
		}

		$Canvas->show();
	}

	function x($x) { return ($x - $this->min_x) * $this->scale + $this->offset; }
	function y($y) { return ($y - $this->min_y) * $this->scale + $this->offset; }
	function sq($x) { return $x*$x; }
}
