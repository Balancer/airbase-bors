<?php

class balancer_tests_bors_demo_jsmod_hello extends bors_module
{
	function body_data()
	{
		if(!($user = object_property(bors()->user(), 'title')))
			$user = ec('Гость с IP=').bors()->client()->ip();

		return array(
			'guest' => $this->get('mode') == 'static' ? '' : $user,
		);
	}
}
