<?php

class balancer_board_sites_reload extends bors_object
{
	function pre_show()
	{
		if(preg_match('!(/sites/\w/\w/(.+?\.(jpe?g|png|gif)))!i', $this->id(), $m))
		{
			$url = 'http://'.$m[2];
			$file = '/var/www/sites.wrk.ru/htdocs'.$m[1];
			if(file_exists($file))
				return bors_message("File $file already exists");

			mkpath(dirname($file), 0777);
			file_put_contents($file, file_get_contents($url));

			if(!file_exists($file))
			{
				$msg = "File $file can't load from $url";
				bors_debug::syslog('warning-lost-image-in-sites', $msg);
				return bors_message($msg);
			}

			return go($this->id());
		}

		bors_debug::syslog('warning-lost-image-in-sites', "Lost image: ".$this->id());
	}

	function reload()
	{
//		var_dump($this->id());
		if(preg_match('!(/sites/\w/\w/(.+?\.(jpe?g|png|gif)))!i', $this->id(), $m))
		{
			$url = 'http://'.$m[2];
			$file = '/var/www/sites.wrk.ru/htdocs'.$m[1];
			if(file_exists($file))
				return "File $file already exists";

			mkpath(dirname($file), 0777);
			file_put_contents($file, file_get_contents($url));

			return NULL;
		}
	}
}
