<?php

class airbase_image extends bors_image
{
//	function db_name() { return config('airbase.db'); }
//	function table_name() { return 'images'; }
//	function table_fields()
//	{
//		return array(
//		);
//	}

	function id_96x96() { return $this->id().',96x96(up,crop)'; }
	function _thumbnail_96x96_def() { return $this->thumbnail('96x96(up,crop)'); }

	function find_containers()
	{
		return balancer_board_posts_object::find_containers($this);
	}

	static function is_logo_valid($image)
	{
/*
		if(config('is_debug') && stripos($image->full_file_name(), 'BmpK_erCMAEkqmv.jpg'))
		{
			echo bors_debug::trace();
			echo '<xmp>';
			var_dump($image->data, $image->thumbnail('96x96')->url());
			exit();
		}
*/
		return $image
			&& file_exists($image->full_file_name())
			&& stripos($image->full_file_name(), '/_cg/') === false
			&& !preg_match('!cache/!', $image->relative_path())
			&& preg_match('!\.(jpe?g|png)$!i', $image->full_file_name())
			&& $image->width() >= 100
			&& $image->height() >= 100
		;
	}

	function image_site_base_url()
	{
		if(preg_match('!^forums/attaches/!', $this->relative_path()))
			return 'http://files.balancer.ru';

		return config('pics_base_url');
	}
}
