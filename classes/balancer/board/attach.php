<?php

require_once BORS_CORE.'/inc/strings.php';

class balancer_board_attach extends balancer_board_object_db
{
	function class_title() { return ec('вложение'); }

	function table_name(){ return 'attach_2_files'; }

	function table_fields()
	{
		return array(
			'id',
			'post_id',
			'owner_id' => 'owner',
			'title' => 'filename',
			'filename',
			'extension',
			'mime',
			'size',
			'downloads',
			'location',
			'new_location',
			'original',
			'source',
			'parent_uri',
		);
	}

function post_id() { return @$this->data['post_id']; }
function set_post_id($v, $dbup=true) { return $this->set('post_id', $v, $dbup); }
function owner_id() { return @$this->data['owner_id']; }
function set_owner_id($v, $dbup=true) { return $this->set('owner_id', $v, $dbup); }
function filename() { return @$this->data['filename']; }
function set_filename($v, $dbup=true) { return $this->set('filename', $v, $dbup); }
function extension() { return @$this->data['extension']; }
function set_extension($v, $dbup=true) { return $this->set('extension', $v, $dbup); }
function mime() { return @$this->data['mime']; }
function set_mime($v, $dbup=true) { return $this->set('mime', $v, $dbup); }
function size() { return @$this->data['size']; }
function set_size($v, $dbup=true) { return $this->set('size', $v, $dbup); }
function downloads() { return @$this->data['downloads']; }
function set_downloads($v, $dbup=true) { return $this->set('downloads', $v, $dbup); }
function location() { return @$this->data['location']; }
function set_location($v, $dbup=true) { return $this->set('location', $v, $dbup); }

	function create_time()
	{
		if($post = $this->post())
			return $post->create_time();

		return filectime($this->file_name_with_path());
	}

	function modify_time()
	{
		if($post = $this->post())
			return $post->create_time();

		return filemtime($this->file_name_with_path());
	}

	function url() { return "http://www.wrk.ru/forums/attachment.php?item=".$this->id(); }

	function thumbnail_link($geometry, $css_class = NULL)
	{
		if(!preg_match("!(jpe?g|png|gif)!i", $this->extension()))
			return '';

		if($css_class)
			$css_class = " class=\"$css_class\"";

		return "<a href=\"{$this->url()}\" class=\"thumbnailed-image-link\"><img src=\"http://files.balancer.ru/cache/forums/attaches/".preg_replace("!/([^/]+)$!", "/$geometry/$1", $this->location())."\" alt=\"\"{$css_class} /></a>";
	}

	function auto_objects()
	{
		return array(
			'post' => 'balancer_board_post(post_id)',
			'owner' => 'balancer_board_user(owner_id)',
		);
	}

	static function show_attaches($post)
	{
		$lcml_registered_attaches = bors_find_all('balancer_board_posts_object', array(
			'post_id' => $post->id(),
			'target_class_id' => bors_foo(__CLASS__)->class_id(),
			'flag' => 'in_post',
		));

		$shown_attache_ids = bors_field_array_extract($lcml_registered_attaches, 'target_object_id');

		$attaches = $post->attaches();

		if(count($attaches) == 1)
		{
			$attach = $attaches[0];

			if(in_array($attach->id(), $shown_attache_ids))
				return NULL;

			return $attach->html('640x480');
		}

		$html = array();
		foreach($attaches as $attach)
		{
			if(!in_array($attach->id(), $shown_attache_ids))
				$html[] = $attach->html(300);
		}

		return join("\n", $html);
	}

	function html($args = array())
	{
		if(!is_array($args))
			$args = array('geo' => $args);

		$size = defval($args, 'geo');

		if(preg_match('/\d*x\d*/', $size))
		{
			$geo = $size;
			list($w, $h) = explode('x', $size);
		}
		else
		{
			$geo = "{$size}x{$size}";
			$w = $size;
			$h = $size;
		}

//		if(config('is_developer')) { var_dump($this->data); exit(); }

		$container = '%s';

		if(preg_match("!(jpe?g|png|gif)!i", $this->extension()))
		{
//			$full_url = 'http://www.balancer.ru/forum/punbb/attachment.php?item='.$this->id().'&download=2&type=.'.$this->extension();
			$full_url = "http://files.balancer.ru/forums/attaches/" . $this->location();
			$thumb_url = "http://files.balancer.ru/cache/forums/attaches/".preg_replace("!/([^/]+)$!", "/{$geo}/$1", $this->location());

			$thumb = "<a href=\"{$full_url}\" class=\"cloud-zoom thumbnailed-image-link\" id=\"zoom-"
				.rand()."\" rel=\"position:'inside'\" title=\""
				.htmlspecialchars($this->title())."\">";

			$thumb .= "<img src=\"{$thumb_url}\" alt=\"\" class=\"main\" class=\"rs_box shadow8\"/></a>";
			$width = $w+16;
			$height = $h+48;

//			$container = "<div style=\"width: ".($w+16)."px; height: ".($h+16)."px; float: left;\">%s</div>";
		}
		elseif(preg_match("!^(mp3)$!i", $this->extension()))
		{
			$full_url = 'http://www.balancer.ru/forum/punbb/attachment.php?item='.$this->id().'&download=2&type=.'.$this->extension();

			$thumb = restore_format(jquery_jplayer::html(array('mp3' => $full_url, 'title' => basename($this->title()))));
			$width = 422;
			set_def($args, 'container_style', "padding: 8px!important; width: {$width}px;");
		}
		elseif(preg_match("!^(flv)$!i", $this->extension()))
		{
			// http://www.balancer.ru/g/p477030
			$ext = $this->extension();
			$full_url = 'http://files.balancer.ru/forums/attaches/'.$this->location();

			$thumb = restore_format(jquery_jplayer::html(array(
				'url' => $full_url,
				'video' => $ext,
				'title' => basename($this->title()),
			)));
			$width = 640;
			set_def($args, 'container_style', "padding: 8px!important; width: {$width}px;");
		}
		else
		{
			$thumb = '';
			$width = 300;
		}

		$container_style = defval($args, 'container_style', "width: {$width}px;");
		$container_class = defval($args, 'container_class', "float_left center mtop8 shadow8");

		$container_style = str_replace('%ATTACH_WIDTH%', "{$width}px", $container_style);

		if($container_style_append = defval($args, 'container_style_append'))
			$container_style_append = ' '.$container_style_append;

		// {$this->url()}
		$html = sprintf($container, "
<div class=\"attach-desc {$container_class}\" style=\"{$container_style}{$container_style_append}\">
	{$thumb}
	<a href=\"{$this->url()}\">".wordwrap($this->title(), 30, ' ', true)." (скачать)</a>
	[".smart_size($this->size()).",&nbsp;{$this->downloads()}&nbsp;".sklon($this->downloads(), 'загрузка', 'загрузки', 'загрузок')."]
	[attach={$this->id()}]
	<div class=\"clear\">&nbsp;</div>
</div>");

		return $html;
	}

	function thumbnail_fast_url($geo)
	{
		return "http://files.balancer.ru/cache/forums/attaches/".preg_replace("!/([^/]+)$!", "/{$geo}/$1", $this->location());
	}

	function image_url()
	{
		if(!preg_match("!(jpe?g|png|gif)!i", $this->extension()))
			return NULL;

		return "http://files.balancer.ru/forums/attaches/{$this->location()}";
//		var_dump($path); exit();
//		return balancer_board_image::register_file($path);
	}

	function file_name_with_path()
	{
		return "/var/www/files.balancer.ru/htdocs/forums/attaches/{$this->location()}";
	}

	function image()
	{
		if(!in_array(strtolower($this->extension()), array('jpg', 'jpeg', 'jpe', 'png', 'gif')))
			return NULL;

		return airbase_image::register_file($this->file_name_with_path());
	}

	function infonesy_uuid()
	{
		return 'ru.balancer.board.attach.' . $this->id();
	}

	function infonesy_push()
	{
		require_once 'inc/functions/fs/file_put_contents_lock.php';
		$storage = '/var/www/sync/airbase-forums-push';
//		$file = $storage.'/attach-'.$this->id().'.json';
		$file = $storage.'/'.$this->infonesy_uuid().'.json';

		$ipfs = new B2\Ipfs\Api("10.0.3.1", "58080", "55001");

		$hash = $ipfs->add(file_get_contents($this->file_name_with_path()));
		$ipfs->pinAdd($hash);

		$data = [
			'UUID'		=> $this->infonesy_uuid(),
			'Node'		=> 'ru.balancer.board',
			'Date'		=> date('r', $this->create_time()),
			'Modify'	=> date('r', $this->modify_time()),
			'Type'		=> 'Attach',
			'IpfsHash'	=> $hash,
			'PostUUID'	=> object_property($this->post(), 'infonesy_uuid'),

			'OwnerEmailMD5' => object_property($this->owner(), 'email_md5'),
			'Filename'	=> $this->title(),
			'MimeType'	=> $this->mime(),
			'Size'		=> $this->size(),
			'Original'	=> $this->original(),
			'Source'	=> $this->source(),
			'ParentURI'	=> $this->parent_uri(),
		];

		@file_put_contents_lock($file, json_encode(array_filter($data), JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		@chmod($file, 0666);

		return $hash;
	}
}
