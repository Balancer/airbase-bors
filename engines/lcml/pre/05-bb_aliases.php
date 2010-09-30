<?php
    function lcml_bb_aliases($txt)
    {
		$txt = preg_replace("!\[ab=\"([^\]]+?)\"\]!is", "[ab user=\"$1\"]", $txt);
		$txt = preg_replace("!\[ab=([^\]]+?)\]!is", "[ab user=\"$1\"]", $txt);

		return $txt;
	}
