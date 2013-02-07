<?php

class airbase_images_show extends base_page
{
	function can_be_empty() { return false; }
	function template() { return 'forum/common.html'; }

	private $image_f = NULL;
	private $image_url = NULL;

	function is_loaded() { return $this->image_f != NULL; }

	function _parent()
	{
		if($parent = bors_load_uri(dirname($this->url()).'/'))
			return $parent;

		return bors_load_uri('/');
	}

	function parents()
	{
//		if($p = parent::parents())
//			return $p;

		return array($this->_parent()->url());
	}

	function nav_name() { return ec('изображение'); }
	function title() { return object_property($this->_parent(), 'title'); }

	function init()
	{
		$data = url_parse($this->id());

		$url_wo_ext = preg_replace('!\.htm$!', '', $data['uri']);
		$url_wo_ext = preg_replace('!^\w+://[^/]+!', '', $url_wo_ext);
		$file_wo_ext = preg_replace('!\.htm$!', '', $data['local_path']); // /*unislash*/($_SERVER['DOCUMENT_ROOT'] . $url_wo_ext);

//		echo "uwo=$url_wo_ext<br/>fwo=$file_wo_ext<Br/>";

		foreach(explode(' ', '.jpg .jpeg .JPG .JPEG .png .PNG .gif .GIF ') as $ext)
		{
			$test_file = $file_wo_ext.$ext;
			if(file_exists($test_file))
			{
				$this->image_f = $test_file;
				$this->image_url = $url_wo_ext.$ext;
				return false;
			}
		}
	}

	private $image = NULL;
	function image()
	{
		if($this->image === NULL)
			$this->image = objects_load();

		return $this->image;
	}

	function image_url() { return $this->image_url; }
	function image_thumb_url($geo) { return preg_replace('!^(.*?)(/[^/]+)$!', "/cache$1/$geo$2", $this->image_url()); }
}
