<?php
function smarty_modifier_get($object, $field)
{
	if(!$object)
		return "get <b>$field</b> for NULL object";

	return $object->$field();
}
