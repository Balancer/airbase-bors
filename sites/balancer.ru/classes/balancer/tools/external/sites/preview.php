<?php

class balancer_tools_external_sites_preview extends bors_image_png
{
	function class_title() { return 'Превью сайта'; }

	function image()
	{
		$id = $this->id();
		$cache_file = $_SERVER['DOCUMENT_ROOT'] . '/_cg/_st/000-long/'.md5($id).'.png';

		if(file_exists($cache_file))
		{
			if(config('is_developer')) { var_dump($cache_file); exit(); }
			return file_get_contents($cache_file);
		}

		$url = bors()->request()->data('url');
		$geo = bors()->request()->data('geo');

		// Это старый формат с прямой ссылкой, без параметров
		if(!$url && $this->page() == '_cg/_st')
		{
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
			$file = rtrim($file, '/');

			$file .= ".png";
			$file = translite_path_simple($file);

			$thumb_url = '/sites/'.preg_replace('!^(.+)(/[^/]+)$!', "$1/{$geo}$2", $file);
			$file = $_SERVER['DOCUMENT_ROOT'].'/sites/'.$file;
			$resize = NULL;
		}

		if(strlen($file) > 200)
			bors_debug::syslog('warning-site-thumbnail', "Too long filename: ".$file.' = '.strlen($file));

		$thumb_file = $_SERVER['DOCUMENT_ROOT'].$thumb_url;

//		bors_debug::syslog('sites_preview', "Thumbnail $url ($geo); ".escapeshellcmd($url));

		$url = blib_urls::parts_encode($url);

		mkpath(dirname($file));

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_exec($ch);

		$mime = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

		if(preg_match('/image/', $mime))
		{
			blib_http::get_ex($url, ['is_raw' => true, 'file' => $file]);

			if($mime != 'image/png')
			{
				system(config('bin.mogrify', "/usr/bin/mogrify")
					." -format png"
					." ".escapeshellcmd($file)
				);
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

		// https://github.com/KnpLabs/snappy/blob/master/src/Knp/Snappy/Image.php
		if(config('bin.wkhtmltoimage') && file_exists(config('bin.wkhtmltoimage')))
			$snappy = new my_snappy(config('bin.wkhtmltoimage'));
		else
			$snappy = new my_snappy(COMPOSER_ROOT . '/vendor/h4cc/wkhtmltoimage-amd64/bin/wkhtmltoimage-amd64');

		$snappy->setTimeout(15);
		$snappy->setOption('width', 1024);
		$snappy->setOption('height', 768);
		$snappy->setOption('minimum-font-size', 20);
		$snappy->setOption('encoding', 'utf-8');
		$snappy->setOption('custom-header', ['User-Agent' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:14.0) Gecko/20100101 Firefox/14.0.1']);
//		$snappy->setOption('load-error-handling', 'ignore');

		// wkhtmltopdf --custom-header "User-Agent" "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:14.0) Gecko/20100101 Firefox/14.0.1"
		// --custom-header-propagation "https://www.google.co.in/search?hl=en&safe=off&client=firefox-beta&hs=1re&tbo=d&rls=org.mozilla%3Aen-US%3Aofficial&channel=fflb&q=height+of+lionel+messi&oq=height+of+lionell&gs_l=serp.3.0.0i13l2j0i13i30.3477969.3479300.0.3480775.7.7.0.0.0.0.233.1161.2j1j4.7.0.les%3B..0.0...1c.1.8wCY6CBhpqY" google.pdf

//		." --crop-w 800 --crop-h 600 --crop-x 200 --crop-y 64"

		$js_disabled = preg_match('!(livejournal\.com|rudis\.ru|lrytas\.lt)!', $url);

		if(config('proxy.force_regexp') && preg_match(config('proxy.force_regexp'), $url))
			$snappy->setOption('proxy',  'http://'.config('proxy.forced'));

		if(!file_exists($file) || !filesize($file))
		{
			@unlink($file);

			$image = NULL;

			if(!$js_disabled)
			{
				try
				{
					$image = $snappy->getOutput($url);
				}
				catch(Exception $e)
				{
 				}
			}

			if(!$image)
			{
				$snappy->setOption('disable-javascript', true);
				try
				{
					$image = $snappy->getOutput($url);
				}
				catch(Exception $e)
				{
					bors_debug::syslog('sites_preview', "Exception: ".$e->getMessage());
//					echo '<xmp>'; var_dump($snappy); exit('</xmp>');
					$image = NULL;
				}
			}

			if(!$image)
			{
				bors_debug::syslog('sites_preview', "Image $url ($geo) error. Zero image", 1);
				return NULL;
			}

			if(strlen($file) <= 200)
			{
				file_put_contents($file, $image);
			}

			if(!file_exists($file) && $image)
			{
				file_put_contents($file = $cache_file, $image);
				file_put_contents($cache_file.'.txt', $url);
//				http://www.balancer.ru/cache/_cg/_st/000-long/400x300/f09effcc2e34e2ccce081713fb770c5d.png
//				$thumb_url = '/cache/sites/'.preg_replace('!^(.+)(/[^/]+)$!', "$1/{$geo}$2", $file);
//				$thumb_file = $_SERVER['DOCUMENT_ROOT'].$thumb_url;
			}
		}

//		if(config('is_developer')) { var_dump($file); exit(); }

		if($geo && $thumb_url)
		{
//			if(config('is_developer')) { var_dump($geo, $thumb_url); exit(); }
			$thumb = bors_load('bors_image_autothumb', $thumb_url);
			$thumb->pre_show();
			if(!file_exists($thumb_file))
			{
				bors_debug::syslog('sites_preview', "Image $url ($geo) thumbnail error", 1);
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

class my_snappy extends Knp\Snappy\Image
{
	protected function checkProcessStatus($status, $stdout, $stderr, $command)
	{
        if (0 !== $status and '' !== $stderr) {
            bors_debug::syslog('sites_preview', 'throw '.sprintf(
                'The exit status code \'%s\' says something went wrong:'."\n"
                .'stderr: "%s"'."\n"
                .'stdout: "%s"'."\n"
                .'command: %s.',
                $status, $stderr, $stdout, $command
            ));
        }
	}
}
