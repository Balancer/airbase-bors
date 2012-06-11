<?php

class balancer_board_user2 extends balancer_board_user
{
	function storage_engine() { return 'bors_storage_mysql'; }

	function avatared_titled_link()
	{
		$html = "<div style=\"float: left!important; width: 54px!important; height: 54px!important; overflow: hidden; padding: 2px;\">";
		$html .= bors_module::mod_html('balancer_board_modules_avatar', array('user'=>$this, 'geo'=>'50x50', 'show_title' => false));
		$html .= "</div>";

		$html .= '&nbsp;'.$this->titled_link();

		return $html;
	}
}
