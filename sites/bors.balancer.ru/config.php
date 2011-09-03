<?php

config_set('cache_static', true);
config_set('classes_auto_base', 'bors_site');
config_set('classes_skip', array('storage_fs_xml', 'page_fs_xml'));
config_set('debug_hidden_log_dir', '/var/www/bors.balancer.ru/htdocs/logs');
config_set('default_template', 'default');
config_set('lcml_cache_disable', false);
config_set('obsolete_use_handlers_system', false);
config_set('page_fs_separate_cache_static', 86400);
config_set('page_fs_cache_static', 86400);

base_object::add_template_data('template_top_menu', array(
	''			=>	'главная',
	'downloads'	=>	'скачать',
	'blog'		=>	'блог',
	'doc'		=>	'документация',
	'projects'	=>	'проекты',
	'contacts'	=>	'контакты',
	'links'		=>	'ссылки',
	'http://balancer.ru/support/viewforum.php?id=60'		=>	'форум',
	'http://trac.balancer.ru/bors-core/'		=>	'Trac',
));

base_object::add_template_data('bottom_counters', 'xfile:bors/site/counters.html');

template_jquery('/jquery-local/js/jquery-1.6.2.min.js');
template_js_include('/jquery-local/js/jquery-ui-1.8.16.custom.min.js');
template_css('/jquery-local/css/smoothness/jquery-ui-1.8.16.custom.css');
