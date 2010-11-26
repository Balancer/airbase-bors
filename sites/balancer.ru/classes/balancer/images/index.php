<?php

require_once('inc/images.php');

class balancer_images_index extends bors_page
{
	function title() { $this->year(); return ec('Изображения за ').month_name($this->month()).' '.$this->year().ec(' года'); }
	function nav_name() { return month_name($this->month()).' '.$this->year().ec(' года'); }
	function template() { return 'blue_spring'; }

	function cache_static()
	{
		if(!config('static_forum'))
			return 0;

		return $this->id() < date('ym') ? rand(100*86400,300*86400) : rand(3*86400, 7*86400);
	}

	function year() { return '20'.substr($this->id(), 0, 2); }
	function month() { return intval(substr($this->id(), 2)); }

	function prev_link()
	{
		$base = $_SERVER['DOCUMENT_ROOT'].'/img/forums';

		$y = $this->year();
		$m = $this->month();
		while($y*100+$m >= 200409)
		{
			$m--;
			if($m <= 0)
			{
				$m = 12;
				$y--;
			}

			$title = month_name($m).' '.$y.ec(' года');
			$p = sprintf("%02d%02d", $y%1000, $m);
			if(file_exists("{$base}/{$p}"))
				return "<a href=\"/img/forums/{$p}/\">".bors_icon('a-prev.png', array('title' => $title))." $title</a>";
		}

		return NULL;
	}

	function next_link()
	{
		$base = $_SERVER['DOCUMENT_ROOT'].'/img/forums';

		$y = $this->year();
		$m = $this->month();
		while($y*100+$m <= date('Ym'))
		{
			$m++;
			if($m > 12)
			{
				$m = 1;
				$y++;
			}

			$title = month_name($m).' '.$y.ec(' года');
			$p = sprintf("%02d%02d", $y%1000, $m);
			if(file_exists("{$base}/{$p}"))
				return "<a href=\"/img/forums/{$p}/\">$title ".bors_icon('a-next.png', array('title' => $title))."</a>";
		}

		return NULL;
	}

	function images_list()
	{
		$base = '/img/forums/'.$this->id();
		$files = array();
		$dh = opendir($_SERVER['DOCUMENT_ROOT'].$base);
		while($file = readdir($dh))
			if(preg_match('/\.(gif|png|jpe?g)$/i', $file))
				$files[] = $file;

		sort($files);

		$images = array();
		foreach($files as $file)
		{
			$img = objects_first('bors_image', array('relative_path' => $base, 'file_name' => secure_path($file)));
			if(!$img)
			{
				$img = object_new('bors_image');
				$img->register_file(secure_path($base.'/'.$file));
				$img->new_instance();
				$img->id();
			}

			$images[] = $img;
		}

		return $images;
	}
}
