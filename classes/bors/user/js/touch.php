<?php

class user_js_touch extends bors_js
{
	function pre_show()
	{
		template_nocache();

		parent::pre_show();

		$this->set_is_loaded(true);

		$time = bors()->request()->data('time');
		$obj  = bors()->request()->data('obj');

		if($obj)
			$obj = bors_load_uri($obj);
		else
			$obj = object_load($this->id());

		if(!$time)
			$time = time();

		if($obj)
		{
			$obj->touch(bors()->user_id(), $time);
			if($x = $obj->get('touch_info'))
			{
				$res = array();
				foreach($x as $k=>$v)
					$res[] = "top.touch_info_{$k} = ".(is_numeric($v) ? $v : "'".addslashes($v)."'");

				return join("\n", $res);
			}
		}

		return 'true;';
	}
}
