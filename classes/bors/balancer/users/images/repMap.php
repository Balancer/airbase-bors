<?php

class balancer_users_images_repMap extends base_image_svg
{
	function show_image()
	{
//		require_once 'Image/Canvas.php';

		$dbh = new driver_mysql('punbb');
		extract($dbh->select('users', '
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
		', array('rep_x<>0', 'order'=>'last_post DESC', 'limit' => 150)));

		$dx = $max_x - $min_x;
		$dy = $max_y - $min_y;

		$dr = $max_r - $min_r;
		$dg = $max_g - $min_g;
		$db = $max_b - $min_b;

		$offset = 50;

		$width = 1024;
		$height = $width*$dy/$dx;

		$scale = ($width - 100) / $dx;

		// change the output format with the first parameter of factory()
		$Canvas =& Image_Canvas::factory('svg', array(
			'width' => $width,
			'height' => round($height),
			'encoding' => 'utf-8'
		));

		$Canvas->setLineColor('black');
//		$Canvas->ellipse(array('x' => 199, 'y' => 149, 'rx' => 50, 'ry' => 50));
		$Canvas->rectangle(array('x0' => $offset/2, 'y0' => $offset/2, 'x1' => $width-$offset/2, 'y1' => $height-$offset/2));

		$Canvas->setLineColor('black');
		$Canvas->line(array('x0' => 0, 'y0' => $min_y, 'x1' => 0, 'y1' => $max_y));
		$Canvas->addText(array('x' => $min_x+10, 'y' => $min_y+10, 'text' => 'Test', 'color' => '#000000'));

		foreach($dbh->select_array('users', '*', array('rep_x<>0', 'order'=>'last_post DESC', 'limit' => 150)) as $r)
		{
			$x = ($r['rep_x']-$min_x)*$scale+$offset;
			$y = ($r['rep_y']-$min_y)*$scale+$offset;

			$cr = ($r['rep_r']-$min_r)/$dr*180+20;
			$cg = ($r['rep_g']-$min_g)/$dg*180+20;
			$cb = ($r['rep_b']-$min_b)/$db*180+20;

			$Canvas->setGradientFill(array(
				'direction' => 'horizontal', 
				'start' => sprintf('#%02x%02x%02x', $cr+20, $cg+20, $cb+20), 
				'end' => sprintf('#%02x%02x%02x', $cr-20, $cg-20, $cb-20), 
			));
			$Canvas->ellipse(array('x' => $x, 'y' => $y, 'rx' => 3, 'ry' => 3));
			$Canvas->setFont(array('name' => 'Verdana', 'size' => 14));
			$Canvas->addText(array('x' => $x, 'y' => $y, 'text' => $r['username'], 'color'=>sprintf('#%02x%02x%02x', $cr, $cg, $cb)));
		}

		$Canvas->show();
	}
}
