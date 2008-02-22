<?php

require_once 'Image/Transform.php';

function image_file_scale($file_in, &$file_out, $width, $height)
{
	$img =& Image_Transform::factory('GD');
	
	if(PEAR::isError($img))
		return $img;

	if(!$width)
		$width = $height * 100 + 64;
	if(!$height)
		$height = $width * 100 + 64;

	$img->load($file_in);
	$img->fit($width, $height);
	$img->save($file_out, $img->getImageType());
	return $img->isError();
}