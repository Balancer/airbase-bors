<?php

class bal_news_page extends bors_view
{
	var $model_class = 'bal_rpc_json_bb_post';
	var $auto_map = true;

	function url() { return '/news/'.date('Y/m/d', $this->post()->create_time()).'/'.$this->id().'.html'; }
	function url_ex($foo) { return $this->url(); }

//	function auto_objects()
//	{
//		return array_merge(parent::auto_objects(), [
//			'post' => 'balancer_board_post(id)',
//		]);
//	}
}
