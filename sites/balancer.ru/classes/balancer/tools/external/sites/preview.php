<?php

class balancer_tools_external_sites_preview extends bors_image_png
{
	function image()
	{
		$url = bors()->request()->data('url');
		$geo = bors()->request()->data('geo');

		if(!$url && $this->page() == '_cg/_st')
		{
			$id = $this->id();
			if(preg_match('/^(\S+)\-(\d+x\d+)$/', $id, $m))
			{
				$url = base64_decode($m[1]);
				$geo = $m[2];
			}
			else
			{
				$url = base64_decode($id);
				$geo = '400x300';
			}

			$store_path = "/_cg/_st/{$id[0]}/{$id[1]}/";
			$file_name = $id.'.png';
			$thumb_url = NULL;
			$file = $_SERVER['DOCUMENT_ROOT'] . $store_path . $file_name;
			$resize = $geo;
		}
		else
		{
			$store_path = "";
			// http://ru.wikipedia.org/wiki/Хвостенко,_Алексей_Львович
			$file = preg_replace('!^http://!', '', $url);
			$file = preg_replace('!^www\.!', '', $file);
			if(preg_match('!/$!', $file))
				$file = substr($file, 0, strlen($file)-1);

			$file .= ".png";
			$file = translite_path_simple($file);

			$thumb_url = '/sites/'.preg_replace('!^(.+)(/[^/]+)$!', "$1/{$geo}$2", $file);
			$file = $_SERVER['DOCUMENT_ROOT'].'/sites/'.$file;
			$resize = NULL;
		}

		$thumb_file = $_SERVER['DOCUMENT_ROOT'].$thumb_url;

		if(!file_exists($file))
		{
			mkpath(dirname($file));
			system(config('bin.wkhtmltoimage', "/opt/bin/wkhtmltoimage-amd64")
				." --width 1024 --height 768"
				." --crop-w 800 --crop-h 600 --crop-x 200 --crop-y 64"
				." --minimum-font-size 20"
				." --enable-plugins"
				." ".escapeshellcmd($url)." ".escapeshellcmd($file)
			);
		}

		if(!file_exists($file))
		{
			debug_hidden_log('sites_preview', "Image $url ($geo) error", 1);
			return NULL;
		}

		if($geo && $thumb_url)
		{
//			var_dump($geo, $thumb_url); exit();
			$thumb = bors_load('bors_image_autothumb', $thumb_url);
			$thumb->pre_show();
			if(!file_exists($thumb_file))
			{
				debug_hidden_log('sites_preview', "Image $url ($geo) thumbnail error", 1);
				return NULL;
			}

			return file_get_contents($thumb_file);
		}

		if($resize)
		{
			system(config('bin.mogrify', "/usr/bin/mogrify")
				." -geometry ".escapeshellcmd($resize)
				." ".escapeshellcmd($file)
			);
		}

		return file_get_contents($file);
	}
}
