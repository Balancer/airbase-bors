<?php

class airbase_web_import_image extends web_import_image
{
	// Ищем локальную копию удалённого изображения в одном из старых форматов
	// Подавать не нормализованный вариант url, так как в старом коде
	// была своя нормализация. Возвращает абсолютный путь к файлу, если будет найден
	static function find_legacy_cached($url)
	{
		extract(parse_url($url));

		//	/var/www/balancer.ru/htdocs/sites
		$store_path = config('sites_store_path');

		// Формат с прямым указанием домена:
		// http://www.hpc.ru/news/pics/5233.jpg
		//	-> ./www.hpc.ru/news/pics

		if(file_exists($f = $store_path.'/'.$host.$path))
			return $f;

		// http://nz.net.ru/photo/2004-01-13.jpg
		//	-> n/z/nz.net.ru/photo/2004-01-13.jpg

		$c1 = bors_substr($host, 0, 1);
		$c2 = bors_substr($host, 1, 1);
		require_once('inc/urls.php');
		$test_path = "$c1/$c2/{$host}".translite_path($path);

		if(preg_match("!/$!", $test_path))
			$test_path .= "index";

		if(!empty($query))
			$test_path .= '/='.str_replace('&','/', $query);

		if(file_exists($f = $store_path.'/'.$test_path))
			return $f;

		return $path = NULL;

		@$uri = html_entity_decode($url, ENT_COMPAT, 'UTF-8');

		// Заменим ссылку в кеш на полную картинку

		$uri = preg_replace("!^(.+?)/cache/(.+)/\d*x\d*/(.+?)$!", "$1/$2/$3", $uri);

		$data = url_parse($uri);

/*
		if($data['local'])
		{
		   	$fp = preg_replace("!^(.*?)/([^/]+)$!", "$1/img/$2", $data['local_path']);
			if(file_exists($fp))
			{
				$path  = $fp; // локальный путь
				$uri   = preg_replace("!^(.*?)/([^/]+)$!", "$1/img/$2", $data['uri']);
			}
			else
			{
				$fp = $data['local_path'];
				if(file_exists($fp))
				{
					$path  = $fp; // локальный путь
					$uri   = $data['uri'];
				}
			}
		}
*/

		$data = url_parse($uri);

/*
		if(!file_exists($path) && $data['local'])
		{
			$path = $data['local_path'];
			$uri  = $data['uri'];
		}
*/

		// ???
		if(preg_match('/\w{5,}$/', $data['path']))
			$data['path'] .= '.jpg';

		//	http://www.balancer.ru/sites
		$store_url  = config('sites_store_url');

		if(preg_match('!/_cg/!', $uri))
		{
			$path = $data['path'];
			$file = $data['local_path'];;
			$store_url  = 'http://www.balancer.ru';
			$store_path = str_replace($path, '', $data['local_path']);
		}
		else
		{
			$path = $data['path'];
			//TODO: Придумать, что сделать с этим хардкодом.
			if(file_exists($data['local_path']) || preg_match('!/var/www!', $data['local_path']))
				$file = $data['local_path'];
			else
				$file = "$store_path$path";
		}

		if(!$data['local'] || !file_exists($file))
		{
			$path = "{$data['host']}{$data['path']}";

			if(preg_match("!/$!",$path))
				$path .= "index";

			if(!empty($data['query']))
				$path .= '/='.str_replace('&','/', $data['query']);

			$file = "$store_path/$path";
			if(!file_exists($file) || filesize($file)==0)
			{
				$c1 = bors_substr($data['host'],0,1);
				$c2 = bors_substr($data['host'],1,1);
				require_once('inc/urls.php');
				$path = "$c1/$c2/{$data['host']}".translite_path($data['path']);

				if(preg_match("!/$!",$path))
					$path .= "index";

				if(!empty($data['query']))
					$path .= '/='.str_replace('&','/', $data['query']);

				$file = "$store_path/$path";
			}

			if(!file_exists($file) || filesize($file)==0 || !($image_size = @getimagesize($file)))
			{
				$path = config('sites_store_path').'/'.web_import_image::storage_place_rel($params['url']);
				$file = "$store_path/$path";
			}

			$image_size = @getimagesize($file);

			if(file_exists($file) && !$image_size)
			{
				//TODO: Придумать, что сделать с этим хардкодом.
				$thumbnails = bors_find_all('bors_image_thumb', array(
					"full_file_name LIKE '%/".addslashes(basename($file))."'",
				));

				if($thumbnails)
					foreach($thumbnails as $t)
						$t->delete();

				unlink($file);
			}

			if(!file_exists($file) || filesize($file)==0 || !$image_size)
			{
				$path = web_import_image::storage_place_rel($params['url']);
				$file = "$store_path/$path";

				require_once('inc/filesystem.php');
				mkpath(dirname($file), 0777);

				if(!is_writable(dirname($file)))
				{
					bors_use('debug_hidden_log');
					bors_debug::syslog('access_error', "Can't write to ".dirname($file));
					return "<a href=\"{$params['url']}\">{$params['url']}</a><small class=\"gray\"> [can't write '$file']</small>";
				}

				$x = blib_http::get_ex(str_replace(' ', '%20', $params['url']), array(
					'file' => $file,
					'is_raw' => true,
				));

				@chmod($file, 0666);

				$content_type = $x['content_type'];

				if(@filesize($file) <= 0)
					return "<a href=\"{$uri}\">{$uri}</a> <small style=\"color: #ccc\">[zero size or time out]</small>";

				// Яндекс.Видео — такое Яндекс.Видео...
				// http://balancer.ru/g/p2728087 для http://video.yandex.ru/users/cnewstv/view/3/
				if($content_type
						&& !preg_match("!image!", $content_type)
						// http://www.balancer.ru/g/p3158050 — овнояндекс отдаёт картинку как text/html
						&& !preg_match('!img-fotki\.yandex\.ru/get/\d+!', $params['url'])
					)
				{
//					bors_debug::syslog('images-error', $params['url'].ec(': is not image. ').$content_type."\n".$content); // Это не картинка
					return lcml_urls_title($params['url']).'<small> [not image]</small>';
				}

				//TODO: придумать, блин, какой-нибудь .d вместо каталогов. А то, вдруг, картинка будет и прямая
				//и с GET-параметрами.

				// Автоматический фикс старого некорректного утягивания.
				// errstr=fopen(/var/www/balancer.ru/htdocs/sites/g/a/gallery.greedykidz.net/get/992865/3274i.jpg/=g2_serialNumber=1)
				if(preg_match('#^(.+\.(jpe?g|png|gif))/=#', $file, $m) && file_exists($m[1]))
					unlink($m[1]);
			}

			$image_size = @getimagesize($file);
			if(file_exists($file) && filesize($file)>0 && $image_size)
			{
				$data['local_path'] = $_SERVER['DOCUMENT_ROOT'] . "/$path";
				$data['local'] = true;
			}

			// test: http://www.aviaport.ru/conferences/40911/rss/
			if(file_exists($file) && filesize($file)>0 && config('lcml.airbase.register.images'))
			{
				$remote = $uri;
				$uri = "$store_url/$path";
				$data['local'] = true;

				$db = new driver_mysql(config('main_bors_db'));

				$id = intval($db->select('images', 'id', array('original_url=' => $remote)));
				if(!$id)
				{
					$db->store('images', 'original_url=\''.addslashes($remote).'\'', array('original_url' => $remote));
					$id = $db->last_id();
				}

				$db->update('images', array('id' => $id), array('local_path' => $data['local_path']));

				$img = airbase_image::register_file($file, true, true, 'airbase_image');
				balancer_board_posts_object::register($img, $params);
			}
		}

		if($data['local'])
		{
			if(!file_exists($file))
			{
				bors_debug::syslog('error_lcml_tag_img', "Incorrect image {$params['url']}");
				return lcml_urls_title($params['url']).'<small> [image link error]</small>';
			}


			if(preg_match('/airbase\.ru|balancer\.ru|wrk\.ru/', $data['uri']) && preg_match('!^(http://[^/]+/cache/.+/)\d*x\d*(/[^/]+)$!', $data['uri'], $m))
				$img_ico_uri  = $m[1].$params['size'].$m[2];
			elseif(!empty($params['noresize']))
				$img_ico_uri  = $uri;
			elseif(preg_match('/airbase\.ru|balancer\.ru|wrk\.ru/', $data['uri']))
				$img_ico_uri  = preg_replace("!^(http://[^/]+)(.*?)(/[^/]+)$!", "$1/cache$2/{$params['size']}$3", $data['uri']);
			else
				$img_ico_uri  = preg_replace("!^(http://[^/]+)(.*?)(/[^/]+)$!", "$1/cache$2/{$params['size']}$3", "$store_url/$path");

			if(preg_match('!\.[^/+]$!', $uri))
				$img_page_uri = preg_replace("!^(http://.+?)(\.[^\.]+)$!", "$1.htm", $uri);
			else
				$img_page_uri = $uri.'.htm';

			if(defval($params, 'is_direct') || defval($params, 'ajax'))
				$img_page_uri = $uri;

			if($href = defval($params, 'href'))
				$have_href = true;
			elseif(defval($params, 'use_cache'))
			{
				$href = $uri;
				$have_href = true;
			}
			else
			{
				$href = $img_page_uri;
				$have_href = false;
			}

			if(!$have_href)
				$href = $uri;

			// Дёргаем превьюшку, чтобы могла сгенерироваться.
			// Кстати, ошибка может быть и от перегрузки. Надо будет сделать прямой вызов
			blib_http::get($img_ico_uri, true, 100000); // До 100кб

			list($width, $height, $type, $attr) = getimagesize($img_ico_uri);

			if(!intval($width) || !intval($height))
			{
				// Если с одного раза не сработало, пробуем ещё раз
				sleep(5);
				blib_http::get($img_ico_uri, true, 1000000); // До 1Мб

				list($width, $height, $type, $attr) = @getimagesize($img_ico_uri);
			}

			if(!intval($width) || !intval($height))
				return "<a href=\"{$params['url']}\">{$params['url']}</a> [can't get <a href=\"{$img_ico_uri}\">icon's</a> size]";

			@list($img_w, $img_h) = getimagesize($uri);

			if(empty($params['description']))
				$params['description'] = "";
			if(empty($params['no_lcml_description']))
				$description = stripslashes(!empty($params['description']) ? lcml($params['description']) : '');
			else
				$description = stripslashes(!empty($params['description']) ? $params['description'] : '');

			$a_href_b = "";
			$a_href_e = "";

			$image_class = array('main');
			$ajax = defval($params, 'ajax');
			$styles = array();

			if($description)
				$title = " title=\"".htmlspecialchars(str_replace('www.', '&#119;ww.', strip_tags($description)))."\"";
			else
				$title = "";

			if(empty($params['nohref']) || $ajax)
			{
				if($img_w < $width*1.1 || $ajax == 'hoverZoom')
				{
					if($ajax == 'hoverZoom')
					{
//							$image_class[] = 'hoverZoom';
							$styles[] = 'hoverZoom';
						}

						$a_href_b = "<a href=\"{$href}\" class=\"thumbnailed-image-link\"{$title}>";
						$a_href_e = "</a>";
					}
					elseif(!preg_match('/\.htm$/', $href))
					{
						if($width > 300 && $height > 200)
							$rel = "position:'inside'";
						else
							$rel = "position:'bototm', zoomWidth:400, zoomHeight:400";

//						$lightbox_code = save_format(jquery_lightbox::html("'a.cloud-zoom'"));
						$lightbox_code = "";
						$a_href_b = "$lightbox_code<a href=\"{$href}\" class=\"cloud-zoom thumbnailed-image-link\" id=\"zoom-".rand()."\" rel=\"{$rel}\"{$title}>";
						$a_href_e = "</a>";
					}
					else
					{
						$a_href_b = "<a href=\"{$href}\"{$title}>";
						$a_href_e = "</a>";
					}

				}

				$out = '';

				if(@$params['border'])
				{
					if($width > 640) // Это чтобы не наезжало на аватар
						$out .= "<div class=\"clear\">&nbsp;</div>\n";

					$params['skip_around_cr'] = true;
					$styles[] = $description ? 'rs_box' : 'rs_box_nd';
				}

				if(@$params['flow'] == 'flow' && @$params['align'] != 'center')
				{
					if(@$params['align'] == 'left')
						$styles[] = 'float_left';
					if(@$params['align'] == 'right')
						$styles[] = 'float_right';
				}
				else
				{
					$styles[] = @$params['align'];
				}

				$styles[] = 'mtop8';
				$description = str_replace('%IMAGE_PAGE_URL%', $img_page_uri, $description);

				$out .= '<div class="'.join(' ', $styles)."\" style=\"width:".($width)."px;".(!$description? "height:".($height)."px" : "")
					.";\">{$a_href_b}<img src=\"$img_ico_uri\" width=\"$width\" height=\"$height\" alt=\"\" class=\"".join(' ', $image_class)."\" />{$a_href_e}";
				if($description)
					$out .= "<small class=\"inbox\">".$description."</small>";
				$out .= '</div>';

				return $out;
			}

		return "<a href=\"{$params['url']}\">{$params['url']}</a><small class=\"gray\"> [can't download]</small>";
	}

	function __dev()
	{
//		echo self::storage_place_rel('http://www.palal.net/blogposts/20130601-favelas/dona%20marta/IMG_9636.JPG'), PHP_EOL;
//		echo self::storage_place_rel('https://pp.vk.me/c540109/c540104/v540104095/d7b2/8zqIgh8Sp3E.jpg'), PHP_EOL;
//		echo self::find_cached('http://www.palal.net/blogposts/20130601-favelas/dona%20marta/IMG_9636.JPG'), PHP_EOL;
//		echo self::find_cached('https://pp.vk.me/c540109/c540104/v540104095/d7b2/8zqIgh8Sp3E.jpg'), PHP_EOL;

		foreach(array(
			'http://www.hpc.ru/news/pics/5233.jpg',
			'http://nz.net.ru/photo/2004-01-13.jpg',
			'http://ханик.рф/uploads/images/00/00/01/2012/02/28/ffa4b5.jpg',
			'http://нюсайт.рф/uploads/posts/2011-09/1316360716_05.jpg',
			'http://www.appleinforma.com/wp-content/uploads/2012/11/empleadas-foxconn.jpg',

			// Эти два — одно и то же:
			'http://провэд.рф/media/k2/items/cache/7e408619b804333fe3c3aa491f931796_S.jpg',
			'http://xn--b1ae2adf4f.xn--p1ai/media/k2/items/cache/7e408619b804333fe3c3aa491f931796_S.jpg',

			// Эти два — одно и то же:
			'http://dammitja.net/lj/opp/pikachu.gif',
			'http://DammitJa.net/lj/opp/pikachu.gif',

			// data.yandex.ru/i?ctype=1&path=a_200490__mod_008_056010.jpg
			'http://data.yandex.ru/i?ctype=1&path=a_200490__mod_008_056010.jpg',
		) as $u)
		{
			$f = self::find_cached($u);
			blib_cli::out(file_exists($f) ? '%g    found%n' : '%rnot found%n');
			echo ': '.$f, PHP_EOL;
		}


	}
}
