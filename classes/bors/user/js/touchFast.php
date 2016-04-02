<?php

class user_js_touchFast extends bors_js
{
	function pre_show()
	{
		template_nocache();

		parent::pre_show();

		$this->set_is_loaded(true);

		$time	= bors()->request()->data('time');
		$obj	= bors()->request()->data('obj');
		$page	= bors()->request()->data('page');
		$me		= bors()->user();
		$me_id	= bors()->user_id();

		if($obj)
			$obj = bors_load_uri($obj);
		else
			$obj = bors_load($this->id());

		if($obj)
			$obj->set_page($page);

		if(!$time)
			$time = time();

		$js = [];

		if(!$me_id || $me_id < 2)
		{
			$js = join("\n", $js);

			if(!$js)
				$js = 'true;';

			return $js;
		}

		$js = join("\n", $js);

		if(!$js)
			$js = 'true;';

		return $js;
	}
}
