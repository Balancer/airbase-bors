<?php
    function lcml_old_bb($txt)
    {
		$txt = preg_replace("!\[ab=\"([^\]]+?)\"\]!is", "[ab user=\"$1\"]", $txt);
		$txt = preg_replace("!\[ab=([^\]]+?)\]!is", "[ab user=\"$1\"]", $txt);

		return $txt;
	}
