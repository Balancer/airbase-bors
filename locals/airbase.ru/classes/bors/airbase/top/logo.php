<?php

class_include('def_image_png');

class airbase_top_logo extends def_image_png
{
	function make_image()
	{
		$id = $this->id();
		$db = &new DataBase('top');
		
		$x = $db->get("SELECT place, visits FROM aviatop_members WHERE id = $id");
		$place  = $x['place'];
		$visits = $x['visits'];
			
		$x  = $db->get("SELECT SUM(visits) as sum, MIN(check_time) as min, MAX(check_time) as max FROM aviatop_week WHERE top_id = $id");
		$rate = $x['sum']*86400/($x['max'] - $x['min']+1);

		$img = ImageCreateFromPNG(dirname(__FILE__)."/aviatop.png");

        $text = ImageColorAllocate ($img, 0x00, 0x33, 0x66);
        $red = ImageColorAllocate ($img, 0xFF, 0x00, 0x00);

        if($place > 0)
        	ImageString($img, 1, 28, 0, sprintf("%3d",$place), $red);

		$this->PutS($img, 0, sprintf("%6d", $visits), $text);
		$this->PutS($img, 8, sprintf("%4d", $rate), $text);

		$path = "/var/www/airbase.ru/htdocs/top/logos";
		include_once("inc/filesystem_ext.php");
		mkpath($path, 0775);
		imagepng($img, "$path/$id.png");
		chmod("$path/$id.png", 0664);

		imagedestroy($img);
			
		return "http://airbase.ru/top/logos/$id.png";
	}

	function image()
	{
		$id = $this->id();
		$db = &new DataBase('top');
		
		$x = $db->get("SELECT place, visits FROM aviatop_members WHERE id = $id");
		$place  = $x['place'];
		$visits = $x['visits'];
			
		$x  = $db->get("SELECT SUM(visits) as sum, MIN(check_time) as min, MAX(check_time) as max FROM aviatop_week WHERE top_id = $id");
		$rate = $x['sum']*86400/($x['max'] - $x['min']+1);

		$img = ImageCreateFromPNG(dirname(__FILE__)."/aviatop.png");

        $text = ImageColorAllocate ($img, 0x00, 0x33, 0x66);
        $red = ImageColorAllocate ($img, 0xFF, 0x00, 0x00);

        if($place > 0)
        	ImageString($img, 1, 28, 0, sprintf("%3d",$place), $red);

		$this->PutS($img, 0, sprintf("%6d", $visits), $text);
		$this->PutS($img, 8, sprintf("%4d", $rate), $text);

		ob_start();
		imagepng($img);
		$png = ob_get_contents();
		ob_end_clean();

		imagedestroy($img);
			
		return $png;
	}

	function PutS($img, $y, $s, $c)
    {
		ImageString($img, 1, 86-ImageFontWidth(1)*strlen($s), $y, $s, $c);
	}

	function url() { return "http://airbase.ru/top/logos/{$this->id()}.png"; }
	
	function cache_static() { return 600; }
}
