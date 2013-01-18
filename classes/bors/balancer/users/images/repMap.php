<?php

class balancer_users_images_repMap extends base_image_svg
{
	function show_image()
	{
//		require_once 'Image/Canvas.php';

		$dbh = new driver_mysql('AB_FORUMS');
		$dbh->query('CREATE TEMPORARY TABLE __minmaxrep SELECT rep_x, rep_y, rep_r, rep_g, rep_b FROM users WHERE rep_x<>0 ORDER BY last_post DESC LIMIT 150');
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

		$this->min_x = $min_x;
		$this->max_x = $max_x;
		$this->min_y = $min_y;
		$this->max_y = $max_y;

		$dx = $max_x - $min_x;
		$dy = $max_y - $min_y;

		$dr = $max_r - $min_r;
		$dg = $max_g - $min_g;
		$db = $max_b - $min_b;

		$this->offset = $offset = 50;

		$width = 1500;
		$height = $width*$dy/$dx;

		$this->scale = $scale = ($width - 100) / $dx;

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

		foreach($dbh->select_array('users', '*', array('rep_x<>0', 'order'=>'last_post DESC', 'limit' => 150)) as $r)
		{
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
		}

		$Canvas->show();
	}

	function x($x) { return ($x - $this->min_x) * $this->scale + $this->offset; }
	function y($y) { return ($y - $this->min_y) * $this->scale + $this->offset; }
	function sq($x) { return $x*$x; }
}
