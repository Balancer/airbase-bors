<?php

class airbase_forum_attach extends base_object_db
{
	function db_name() { return config('punbb.database', 'punbb'); }
	function table_name(){ return 'attach_2_files'; }
	function storage_engine() { return 'bors_storage_mysql'; }

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
		);
	}

function post_id() { return @$this->data['post_id']; }
function set_post_id($v, $dbup) { return $this->set('post_id', $v, $dbup); }
function owner_id() { return @$this->data['owner_id']; }
function set_owner_id($v, $dbup) { return $this->set('owner_id', $v, $dbup); }
function filename() { return @$this->data['filename']; }
function set_filename($v, $dbup) { return $this->set('filename', $v, $dbup); }
function extension() { return @$this->data['extension']; }
function set_extension($v, $dbup) { return $this->set('extension', $v, $dbup); }
function mime() { return @$this->data['mime']; }
function set_mime($v, $dbup) { return $this->set('mime', $v, $dbup); }
function size() { return @$this->data['size']; }
function set_size($v, $dbup) { return $this->set('size', $v, $dbup); }
function downloads() { return @$this->data['downloads']; }
function set_downloads($v, $dbup) { return $this->set('downloads', $v, $dbup); }
function location() { return @$this->data['location']; }
function set_location($v, $dbup) { return $this->set('location', $v, $dbup); }

	function url() { return "http://balancer.ru/forum/punbb/attachment.php?item=".$this->id(); }

	function thumbnail_link($geometry, $css_class = NULL)
	{
		if(!preg_match("!(jpe?g|png|gif)!i", $this->extension()))
			return '';

		if($css_class)
			$css_class = " class=\"$css_class\"";

		return "<a href=\"{$this->url()}\"><img src=\"http://files.balancer.ru/cache/forums/attaches/".preg_replace("!/([^/]+)$!", "/$geometry/$1", $this->location())."\" alt=\"\"{$css_class} /></a>";
	}

	function auto_objects()
	{
		return array(
			'post' => 'balancer_board_post(post_id)',
		);
	}

	static function show_attaches($post)
	{
		$attaches = $post->attaches();
		if(count($attaches) == 1)
		{
			$attach = $attaches[0];
			return $attach->html(640);
		}

		$html = array();
		foreach($attaches as $attach)
			$html[] = $attach->html(300);

		return join("\n", $html);
	}

	function html($size)
	{
		$width = $size;

		if(preg_match("!(jpe?g|png|gif)!i", $this->extension()))
		{
			$thumb_url = "http://files.balancer.ru/cache/forums/attaches/".preg_replace("!/([^/]+)$!", "/{$size}x{$size}/$1", $this->location());
			if($ss = @getimagesize($thumb_url))
			{
				$width = @$ss[0];
				$height = @$ss[1];
				$wxh = @$ss[3];
				$thumb = "<a href=\"{$this->url()}\"><img src=\"{$thumb_url}\" {$wxh} alt=\"\" class=\"main\" /></a>";
			}
			else
			{
				$thumb = ec('Ошибка изображения');
				$width = 300;
			}
		}
		else
		{
			$thumb = '';
			$width = 300;
		}

		return "<div class=\"rs_box float_left center\" style=\"width: {$width}px;\">{$thumb}<br/>"
			."<a href=\"{$this->url()}\">".wordwrap($this->title(), 30, ' ', true)."</a> "
			."[".smart_size($this->size()).",&nbsp;{$this->downloads()}&nbsp;".sklon($this->downloads(), 'загрузка', 'загрузки', 'загрузок')."]</div>";
	}
}
