<?php

/*
	Изображение, хранящееся в каждом каталоге /sites/
	Результат утягивания картинок с чужих сайтов

	Новый формат: /sites/ru/aviaport/path/to/image
*/

class airbase_images_site extends airbase_image
{
	// Собственно, место хранения коллекции.
	static function storage_path() { return '/var/www/balancer.ru/htdocs/sites'; }
	static function storage_url()  { return 'http://www.balancer.ru/sites'; }

	// Метод регистрации чужих URL.
	static function url_register_class() { return 'airbase_external_url'; }

	// Отдельный метод преобразования ссылки в путь.
	// Может меняться только он
	// Новый формат без лишних подкаталогов: /sites/ru/aviaport/path/to/image
	static function path_generator($url)
	{
		$url = urldecode($url);

		// Парсим ссылку
		extract(parse_url($url));

		// Нафиг www. в имени хоста
		$host = preg_replace('!^www\.!', '', $host);

		// Не пишем также номер порта
		$host = preg_replace('/:\d+$/', '', $host);

		// Режем хост по точкам, реверсируем, собираем со слешами:
		// plus.google.com  → com/google/plus
		// Раньше было так: → com/go/google/plus
		// Но это избыточно. Совместимость пусть решается на web-сервере.
		$host_path = array_filter(explode('.', $host));
		$host_path[0] = $host;
		$host_path = join('/', array_reverse($host_path));

		// Если оканчивается на слеш, впишем «файл-заглушку»
		if(preg_match('!/$!',$path))
			$path .= 'index';

/*
		Теперь — работа с параметрами запроса, если они есть

		Старые форматы:
			/data.yandex.ru/i?ctype=1&path=a_200490__mod_008_056010.jpg
			/l/h/lh3.ggpht.com/vCzFtrknNtfHfZ8c_fp3cTxGxPsxf0pyU_eDnUaxUO0pa-xub3Rgz9Rg2vaKWn_fY6N7=w124
			/g/r/groupava1.odnoklassniki.ru/getImage.jpg/=photoId=160434127491/photoType=5
			/com/yt/ytimg/i1/vi/g-DyYiTTq4Y/hqdefault.jpg,feature=og
*/
		if(!empty($query))
		{
			// Оставляем формат совместимым с последним форматом в теге img:
			// web_import_image::storage_place_rel
			if(!empty($query))
			{
				$qparts = explode('&', $query);
				sort($qparts);
				$path .= ','.join('/', $qparts);
			}
		}

		// Итоговый путь
		return $host_path.$path;
	}

	// Проверим, не существует ли уже такой файл.
	// Если существует, возвращаем объект image (self)
	// Автоматически не регистрируем, так как это может быть
	// простой поиск класса-владельца изображения.
	function url_exists($url)
	{
		$path = self::path_generator($url);
		$file = self::storage_path().'/'.$path;
		if(!file_exists($file))
			return false;

		// Если файл на диске существует, то
		// Проверим, зарегистрирован ли URL
		// Если не зарегистрирован, то регистрируем (файл-то уже у нас на диске)
		$cls = self::url_register_class();
		$x = $cls::find_or_register($url);
		$image = self::register_file($file);
		$x->set_local_file($file);
		$x->set_target_class($image->class_name());
		$x->set_target_id($image->id());
		return $image;
	}

	// Собственно, импорт с удалённого URL. Возвращаем объект image (себя)
	function register_url($url)
	{
		$path = self::path_generator($url);
		$file = self::storage_path().'/'.$path;

		// Если файл уже был импортирован
		if(file_exists($file))
		{
			// Проверим, вдруг уже зарегистрирован, если да — то возврат
			if($image = self::file_exists($file))
				return $image;

			// Не зарегистрирован. Регистрируем, прописываем учёт URL и выходим.
			return self::register_file_and_url($file, $url);
		}

		// Если файл не существует, то закачиваем его
		require_once('inc/filesystem.php');
		mkpath($lp = dirname($file), 0777);

		if(!is_writable($lp))
		{
			bors_use('debug_hidden_log');
			debug_hidden_log('access_error', "Can't write to ".$lp);
			return NULL;
		}

		$x = blib_http::get_ex($url, array(
			'file' => $file,
			'is_raw' => true,
		));

		chmod($file, 0666);

		return self::register_file_and_url($file, $url);
	}

	static function register_file_and_url($file, $url)
	{
		$image = self::register_file($file);
		$ucls = self::url_register_class();
		$urlx = $ucls::find_or_register($url);
		$urlx->set_local_file($file);
		$urlx->set_target_class($image->class_name());
		$urlx->set_target_id($image->id());
		$image->set_original_url($url);
		return $image;
	}

	// Всё в одном флаконе. На входе URL, на выходе объект image.
	static function find_or_register_url($url)
	{
		if($image = self::url_exists($url))
			return $image;

		return self::register_url($url);
	}

	function __dev()
	{
//		$img = self::find_or_register_url('http://www.palal.net/blogposts/20130601-favelas/dona%20marta/IMG_9636.JPG');
		$img = self::find_or_register_url('http://data.yandex.ru/i?ctype=1&path=a_200490__mod_008_056010.jpg');
		echo $img->id(), PHP_EOL;
		echo $img->url(), PHP_EOL;
		echo $img->thumbnail('200x200')->url(), PHP_EOL;

//		echo self::path_generator('http://www.palal.net/blogposts/20130601-favelas/dona%20marta/IMG_9636.JPG'), PHP_EOL;
//		echo self::path_generator('http://data.yandex.ru/i?ctype=1&path=a_200490__mod_008_056010.jpg'), PHP_EOL;
	}
}
