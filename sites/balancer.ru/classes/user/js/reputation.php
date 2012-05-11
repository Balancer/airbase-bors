<?php

class user_js_reputation extends base_js
{
	function body_data()
	{
		$me_id = 0;
		$me = bors()->user();
		if($me)
		{
			$me_id = $me->id();

			if($me->warnings() > 3)
				$me_id = 0;

		}

		return array(
			'user_id' => $this->id(),
			'me_id'	=> $me_id,
			'ref' => @$_GET['ref'],
		);
	}
}
