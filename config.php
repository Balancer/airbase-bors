<?php

config_set('cache_static', true);
config_set('debug_hidden_log_dir', '/var/www/bors.balancer.ru/htdocs/logs');
//config_set('debug_mysql_queries_log', 10);
config_set('default_template', 'default');
config_set('lcml_cache_disable', false);
config_set('obsolete_use_handlers_system', false);
config_set('page_fs_separate_cache_static', 0);

base_object::add_template_data('template_top_menu', array(
	''			=>	'главная',
	'downloads'	=>	'скачать',
	'doc'		=>	'документация',
));
