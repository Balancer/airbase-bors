<?php

class balancer_board_image extends bors_image
{
	function id_96x96() { return $this->id().',96x96(up,crop)'; }
	function _thumbnail_96x96_def() { return $this->thumbnail('96x96(up,crop)'); }
}
