<?php

function smarty_modifier_nav_named_links_join($array)
{
	return join(', ', bors_field_array_extract($array, 'nav_named_link'));
}
