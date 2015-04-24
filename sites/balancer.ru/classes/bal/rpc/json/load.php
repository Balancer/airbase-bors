<?php

class bal_rpc_json_load extends bors_json
{
	function data()
	{
		if(!in_array($_SERVER['REMOTE_ADDR'], config('trusted.ips')))
			return ['REMOTE_ADDR' => $_SERVER['REMOTE_ADDR']];

		$object = bors_load($this->args('target_class'), $this->id());

		if(!($fields = $this->args('fields')))
			return $object->data;

		$result = $object->data;
		foreach(explode(',', $fields) as $f)
			$result[$f] = $object->get($f);

		return $result;
	}
}
