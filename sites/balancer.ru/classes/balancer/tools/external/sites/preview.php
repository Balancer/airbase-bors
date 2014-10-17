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
				$url = blib_string::base64_decode2($m[1]);
				$geo = $m[2];
			}
			else
			{
				$url = blib_string::base64_decode2($id);
				$geo = '400x300';
			}

			$url_data = parse_url($url);
			$host = preg_replace('/^www\./', '', $url_data['host']);
			$host_parts = array_reverse(explode('.', $host));

			$id = blib_string::base64_encode2($url);

			$store_path = "/_cg/_st/{$host_parts[0]}/{$host_parts[1][0]}/{$host_parts[1]}/";

			$file_name = "{$id}-{$geo}.png";
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

//		debug_hidden_log('sites_preview', "Thumbnail $url ($geo); ".escapeshellcmd($url));

		$url = blib_urls::parts_encode($url);

		mkpath(dirname($file));

		// https://github.com/KnpLabs/snappy/blob/master/src/Knp/Snappy/Image.php
		$snappy = new Knp\Snappy\Image(COMPOSER_ROOT . '/vendor/h4cc/wkhtmltoimage-amd64/bin/wkhtmltoimage-amd64');

		$snappy->setTimeout(10);
		$snappy->setOption('width', 1024);
		$snappy->setOption('height', 768);
		$snappy->setOption('minimum-font-size', 20);
		$snappy->setOption('encoding', 'utf-8');
//		." --crop-w 800 --crop-h 600 --crop-x 200 --crop-y 64"

		if(!file_exists($file) || !filesize($file))
		{
			@unlink($file);
			file_put_contents($file, $snappy->getOutput($url));
		}

		if(!file_exists($file) || !filesize($file))
		{
			@unlink($file);
			$snappy->setOption('disable-javascript', true);
			file_put_contents($file, $snappy->getOutput($url));
		}

		if(!file_exists($file))
		{
			debug_hidden_log('sites_preview', "Image $url ($geo) error. File not exists", 1);
			return NULL;
		}

		if(!filesize($file))
		{
			debug_hidden_log('sites_preview', "Image $url ($geo) error: zero size", 1);
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
