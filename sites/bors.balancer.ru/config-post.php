<?php

bors_object::add_template_data('template_top_menu', array(
	''			=>	'главная',
	'downloads'	=>	'скачать',
	'blog'		=>	'блог',
	'doc'		=>	'документация',
	'projects'	=>	'проекты',
	'contacts'	=>	'контакты',
	'links'		=>	'ссылки',
	'http://www.balancer.ru/support/viewforum.php?id=60'		=>	'форум',
	'http://trac.balancer.ru/bors-core/'		=>	'Trac',
));

bors_object::add_template_data('bottom_counters', 'xfile:bors/site/counters.html');

//template_jquery('/jquery-local/js/jquery-1.6.2.min.js');
//template_js_include('/jquery-local/js/jquery-ui-1.8.16.custom.min.js');
//template_css('/jquery-local/css/smoothness/jquery-ui-1.8.16.custom.css');

template_jquery_ui();
