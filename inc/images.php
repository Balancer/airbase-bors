<?php

require_once 'Image/Transform.php';

function image_file_scale($file_in, &$file_out, $width, $height)
{
	$img =& Image_Transform::factory('GD');
	
	if(PEAR::isError($img))
		return $img;
	
	$img->load($file_in);
	$img->fit($width, $height);
	$img->save($file_out, $img->getImageType());
	return $img->isError();
}
