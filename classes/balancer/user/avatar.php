<?php

class balancer_user_avatar extends base_object_db
{
	function storage_engine() { return 'bors_storage_mysql'; }

	function db_name() { return 'punbb'; }
	function table_name() { return 'avatars'; }

	function table_fields()
	{
		return array(
			'id',
			'user_id',
			'image_class_name',
			'image_id',
			'raw_image_html' => 'image_html',
			'image_original_url',
			'image_file',
			'title',
			'signature',
			'signature_html',
			'create_time',
		);
	}

	function image_html($geo = '100x100', $image_class = NULL)
	{
		if($this->raw_image_html())
			return $this->raw_image_html();
		return $this->set_raw_image_html($this->image()->thumbnail($geo)->html_code($image_class), true);
	}

	function auto_targets()
	{
		return array_merge(parent::auto_targets(), array(
			'raw_image' => 'image_class_name(image_id)',
		));
	}

	function image()
	{
		if($this->raw_image())
			return $this->raw_image();

		if(!$this->image_original_url())
			return $this->_make_gravatar();

		require_once('inc/http.php');
		$image = http_get($this->image_original_url());
		if(!$image)
			return $this->_make_gravatar();

		$tmp = tempnam('/tmp', 'avatar-register');
		file_put_contents($tmp, $image);
		chmod($tmp, 0666);

		$image_data = getimagesize($tmp);
		unlink($tmp);

		if(!@$image_data[0])
			return $this->_make_gravatar();

		switch ($image_data['mime'])
		{
			case "image/gif":
				$ext = 'gif';
				break;
			case "image/jpeg":
				$ext = 'jpeg';
				break;
			case "image/png":
				$ext = 'png';
				break;
			default:
				return $this->_make_gravatar();
				break;
		}

		$path = '/data/var/www/balancer.ru/data/avatars/'.intval($this->id()/1000);
		mkpath($path, 0777);
		$image_file = $path.'/'.$this->id().'.'.$ext;
		file_put_contents($image_file, $image);
		$image_data = getimagesize($image_file);
		if(!@$image_data[0])
			return $this->_make_gravatar();
		$this->set_image_file($image_file, true);
		$image = bors_image::register_file($image_file);
		$this->set_image_class_name($image->class_name(), true);
		$this->set_image_id($image->id(), true);
		$image->set_relative_path('/avatars/'.intval($this->id()/1000), true);
		return $image;
	}

	private function _make_gravatar()
	{
	}
}
