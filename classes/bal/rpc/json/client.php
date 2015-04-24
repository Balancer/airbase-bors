<?php

class bal_rpc_json_client extends bors_object
{
	function can_be_empty() { return false; }

	function title() { return @$this->data['title']; }

	function data_load()
	{
		$url = "http://www.balancer.ru/rpc/json/load/{$this->remote_class()}/{$this->id()}/{$this->remote_fields}";
		$data = json_decode(file_get_contents($url), true);

		if(count($data) == 1 && !empty($data['REMOTE_ADDR']))
			return bors_throw('Unknown addr '.$data['REMOTE_ADDR']);

		$this->data = $data;

		$this->set_is_loaded(true);
		return true;
	}
}
