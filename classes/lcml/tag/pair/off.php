<?php

class lcml_tag_pair_off extends bors_lcml_tag_pair
{
	function html($content, &$params = array())
	{
		return $this->lcml("[color=\"#bbb\"]{$content}[/color]");
	}
}
