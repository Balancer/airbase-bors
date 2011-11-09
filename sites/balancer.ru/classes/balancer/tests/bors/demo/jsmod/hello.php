<?php

class balancer_tests_bors_demo_jsmod_hello extends bors_module
{
	function body_data()
	{
		return array(
			'guest_ip' => $this->get('mode') == 'static' ? 'xxx.xxx.xxx.xxx' : bors()->client()->ip(),
		);
	}
}
