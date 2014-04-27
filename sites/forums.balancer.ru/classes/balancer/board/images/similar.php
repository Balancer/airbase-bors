<?php

class balancer_board_images_similar extends balancer_board_page
{
	var $title = 'Поиск похожих изображений';
	var $nav_name = 'похожие';

	function get_url() { return trim(bors()->request()->data('url')); }

	function body_data()
	{
		$data = parent::body_data();
		$url = $this->get_url();

		if(!$url)
			return $data;

		$file = airbase_web_import_image::find_cached($url);
		if(!file_exists($file))
		{
			require_once('inc/filesystem.php');
			mkpath(dirname($file), 0777);

			if(!is_writable(dirname($file)))
			{
				bors_debug::syslog('access_error', "Can't write to ".dirname($file));
				return $data;
			}

			$x = blib_http::get_ex(str_replace(' ', '%20', $url), array(
				'file' => $file,
				'is_raw' => true,
			));

			@chmod($file, 0666);
		}

		$img = airbase_image::register_file($file, true, true, 'airbase_image');

		$data['image'] = $img;

		$data['similar_images'] = bors_find_all('airbase_image', array(
			'id<>' => $img->id(),
//			'hash_y' => $img->hash_y(),
			'hash_r' => $img->hash_r(),
			'hash_g' => $img->hash_g(),
			'hash_b' => $img->hash_b(),
			'order' => '-create_time',
		));

		return $data;
	}
}
