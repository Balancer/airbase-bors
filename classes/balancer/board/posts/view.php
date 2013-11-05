<?php

class balancer_board_posts_view extends balancer_board_view
{
	function model_class() { return 'balancer_body_post'; }

	static function container_init()
	{
		jquery::load();
		jquery::plugin('cookie');
		jquery::on_ready(__DIR__.'/view.container-ready.js');
	}

	function post() { return $this->model(); }

	function model()
	{
		if(is_object($this->id()))
			return $this->id();

		if(is_numeric($this->id()))
			return bors_load('balancer_board_post', $this->id());

		return NULL;
	}

	function set_body_template($body_template_name)
	{
		if(preg_match('/^[\w\-]+$/', $body_template_name))
			$body_template_name = preg_replace('/^(.+)\.([^\.]+)$/', "$1.$body_template_name.$2", parent::body_template());

		$this->set_attr('body_template', $body_template_name);

		return $this;
	}

	function is_public_access() { return $this->post()->topic()->is_public_access(); }
}
