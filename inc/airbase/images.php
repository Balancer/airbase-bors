<?php

function airbase_image_data($image_name, $base_url = NULL)
{
	if(substr($base_url, -1) != '/')
		$base_url = dirname($base_url) . '/';

	$image_name = abs_path_from_relative($image_name, "{$base_url}img/");
	$data = url_parse($image_name);

	return $data;
}
