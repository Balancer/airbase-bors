<?php

class airbase_web_file extends base_object_db
{
	function db_name() { return 'AIRBASE'; }
	function table_name() { return 'web_files_cache'; }

	function table_fields()
	{
		return array(
			'id',
			'web_url' => 'url',
			'file',
			'file_original',
			'size',
			'mime',
			'title',
			'file_time' => array('field' => 'UNIX_TIMESTAMP(file_time)'),
			'file_time_original',
			'text' => array('type' => 'bbcode'),
			'create_time' => array('field' => 'UNIX_TIMESTAMP(create_time)'),
			'modify_time' => array('field' => 'UNIX_TIMESTAMP(modify_time)'),
		);
	}

//	function ignore_on_new_instance() { return true; }
	function replace_on_new_instance() { return true; }

	static function register($url, $file, $original_file_time, $mime)
	{
		while(preg_match('/%\w{2}/', $url))
			$url = urldecode($url);

		$time = strtotime($original_file_time);
		if($time && $time > time()-30)
			$time = NULL;

		bors_new('airbase_web_file', array(
			'web_url' => $url,
			'file' => $file,
			'file_original' => "$file.original",
			'size' => filesize($file),
			'mime' => $mime ? $mime : mime_content_type($file),
//			'title',
			'file_time' => $time,
			'file_time_original' => $original_file_time,
		));
	}
}
