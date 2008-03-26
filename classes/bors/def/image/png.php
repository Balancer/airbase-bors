<?php

class def_image_png extends base_object
{
	function can_be_empty() { return true; }

	function render_engine() { return 'def_image_png'; }

	function render($object)
	{
		header("Content-type: " . image_type_to_mime_type(IMAGETYPE_PNG));
		return $object->image();
	}
}
