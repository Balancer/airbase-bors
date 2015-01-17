<?php

class airbase_images_show extends balancer_board_page
{
	function can_be_empty() { return false; }
	function template() { return 'forum/page.html'; }

	function title()
	{
		if(object_property($this->_parent(), 'class_name') != 'balancer_main')
			return object_property($this->_parent(), 'title');

		return 'Изображение ' . basename($this->image_file);
	}

	function nav_name() { return ec('изображение'); }

	private $image_file = NULL;
	private $image_url = NULL;

	function is_loaded() { return $this->image_file != NULL; }

	function _parent()
	{
		$ps = $this->parents();
		if(is_object($ps[0]))
			return $ps[0];

		return bors_load_uri($ps[0]);
	}

	var $__containers = false;
	function containers()
	{
		if($this->__containers === false)
		{
			if($this->image())
			{
				$this->__containers = bors_field_array_extract(bors_find_all('balancer_board_posts_object', array(
//					'target_class_name IN' => array('bors_image', 'airbase_image'),
					'target_object_id' => $this->image()->id(),
				)), 'post');
			}
			else
				$this->__containers = NULL;
		}

		return $this->__containers;
	}

	var $parents = false;
	function parents()
	{
		if($this->parents !== false)
			return $this->parents;

		if($cs = $this->containers())
			return $this->parents = bors_field_array_extract($cs, 'url_for_igo');

		if($parent = bors_load_uri(dirname($this->url()).'/'))
			return $this->parents = array($parent);

		return $this->parents = array(bors_load_uri('/'));
	}

	function data_load()
	{
		$data = url_parse($this->id());

		$url_wo_ext = preg_replace('!\.htm$!', '', $data['uri']);

		$url_wo_ext = preg_replace('!^\w+://[^/]+!', '', $url_wo_ext);
		$file_wo_ext = preg_replace('!\.htm$!', '', $data['local_path']); // /*unislash*/($_SERVER['DOCUMENT_ROOT'] . $url_wo_ext);

		if(preg_match('/\.(jpe?g|png|gif)$/i', $file_wo_ext))
		{
			if(file_exists($file_wo_ext))
			{
				$this->image_file = $file_wo_ext;
				$this->image_url = $url_wo_ext;
				return;
			}

			if(file_exists($f = urldecode($file_wo_ext)))
			{
				$this->image_file = $f;
				$this->image_url = urldecode($url_wo_ext);
				return;
			}
		}
//		echo "uwo=$url_wo_ext<br/>fwo=$file_wo_ext<Br/>";

		foreach(explode(' ', 'jpg jpeg JPG JPEG png PNG gif GIF') as $ext)
		{
			$test_file = "{$file_wo_ext}.{$ext}";
//			echo "$test_file<br/>";
			if(file_exists($test_file))
			{
				$this->image_file = $test_file;
				$this->image_url = "{$url_wo_ext}.{$ext}";
				return false;
			}
		}
	}

	private $image = false;
	function image()
	{
		if($this->image === false)
			$this->image = airbase_image::register_file($this->image_file);

		return $this->image;
	}

	function image_url() { return $this->image_url; }
	function image_thumb_url($geo) { return preg_replace('!^(.*?)(/[^/]+)$!', "/cache$1/$geo$2", $this->image_url()); }

	function body_data()
	{
		$data = parent::body_data();
		if(!($image = $this->image()))
			return $data;

		$data['image'] = $image;
		$data['objects'] = $this->containers();

		return $data;
	}
}
