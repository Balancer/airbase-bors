<?php

class user_js_reputation extends base_js
{
	function local_template_data_set()
	{
		return array(
			'user_id' => $this->id(),
			'me_id'	=> bors()->user() ? bors()->user()->id() : 0,
			'ref' => @$_GET['ref'],
		);
	}
}
