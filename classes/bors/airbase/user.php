<?php

class airbase_user extends forum_user
{
//	function reputation_score() { $rep = $this->reputation(); return atan($rep*$rep/($rep >= 0 ? 300 : 100))*2/pi()); }
	function reputation_factor() { $x = (atan($this->reputation()*2/pi())+1)/2 * $this->weight(); return $x*$x; }
	function warnings_factor() { $x = atan($this->warnings()/pi()); return $x*$x; }

	function update_karma($score)
	{
		$this->set_karma($this->karma() + $score * $this->reputation_factor() +1, true);
	}
}
