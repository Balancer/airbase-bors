<?php

class bal_rpc_json_client extends bors_object
{
	function can_be_empty() { return false; }

	function data_load()
	{
		$url = "http://www.balancer.ru/rpc/json/load/{$this->remote_class()}/{$this->id()}/{$this->remote_fields}";
		$data = json_decode(file_get_contents($url), true);
		r($data);

		$this->data = $data;

		return true;
	}
}
