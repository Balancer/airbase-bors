<?php
class_include('def_object');

class def_image_gif extends def_object
{
	function can_be_empty() { return true; }

	function render_engine() { return 'def_image_gif'; }

	function render($object)
	{
		header("Content-type: " . image_type_to_mime_type(IMAGETYPE_GIF));
		return $object->image();
	}
}
