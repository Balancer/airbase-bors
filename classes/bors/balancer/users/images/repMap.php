<?php

class balancer_users_images_repMap extends base_image_svg
{
	function show_image()
	{
		require_once 'Image/Canvas.php';

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
		', array()));

		$dx = $max_x - $min_x;
		$dy = $max_y - $min_y;

		$dr = $max_r - $min_r;
		$dg = $max_g - $min_g;
		$db = $max_b - $min_b;

		$offset = 50;

		$scale = 5;

		// change the output format with the first parameter of factory()
		$Canvas =& Image_Canvas::factory('svg', array('width' => round($dx*$scale+2*$offset), 'height' => round($dy*$scale+2*$offset), 'encoding' => 'utf-8'));

		$Canvas->setLineColor('black');
//		$Canvas->ellipse(array('x' => 199, 'y' => 149, 'rx' => 50, 'ry' => 50));
		$Canvas->rectangle(array('x0' => $offset-10, 'y0' => $offset-10, 'x1' => $dx*$scale-$offset+10, 'y1' => $dy*$scale-$offset+10));

//		echo ($dx*$scale)."\n";
		foreach($dbh->select_array('users', '*', array('rep_x<>0', 'order'=>'last_post DESC', 'limit' => 150)) as $r)
		{
//			print_d($x);
//			echo (($x['rep_x']-$min_x)*$scale)."<br/>\n";
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
//			$Canvas->setLineColor('yellow');
			$Canvas->addText(array('x' => $x, 'y' => $y, 'text' => $r['username'], 'color'=>sprintf('#%02x%02x%02x', $cr, $cg, $cb)));
		}
/*

		$Canvas->setGradientFill(array('direction' => 'horizontal', 'start' => 'red', 'end' => 'blue'));
		$Canvas->setLineColor('black');

		$Canvas->setFont(array('name' => 'Arial', 'size' => 12));
		$Canvas->addText(array('x' => 0, 'y' => 0, 'text' => 'Demonstration of what Image_Canvas do!'));

		$Canvas->setFont(array('name' => 'Times New Roman', 'size' => 12));
		$Canvas->addText(array('x' => 399, 'y' => 20, 'text' => 'This does not demonstrate what is does!', 'alignment' => array('horizontal' => 'right')));

		$Canvas->setFont(array('name' => 'Courier New', 'size' => 7, 'angle' => 270));
		$Canvas->addText(array('x' => 350, 'y' => 50, 'text' => 'True, but it\'s all independent of the format!', 'alignment' => array('horizontal' => 'right')));

		$Canvas->setFont(array('name' => 'Garamond', 'size' => 10));
		$Canvas->addText(array('x' => 199, 'y' => 295, 'text' => '[Changing format is done by changing 3 letters in the source]', 'alignment' => 
		array('horizontal' => 'center', 'vertical' => 'bottom')));

		$Canvas->addVertex(array('x' => 50, 'y' => 200));
		$Canvas->addVertex(array('x' => 100, 'y' => 200));
		$Canvas->addVertex(array('x' => 100, 'y' => 250));
		$Canvas->setFillColor('red@0.2');
		$Canvas->polygon(array('connect' => true));

//		$Canvas->image(array('x' => 398, 'y' => 298, 'filename' => './pear-icon.png', 'alignment' => array('horizontal' => 'right', 'vertical' => 'bottom')));
*/
		$Canvas->show();		
	}

	function cache_static() { return rand(3600, 7200); }
}
