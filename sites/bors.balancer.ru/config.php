<?php

config_set('cache_static', true);
config_set('classes_auto_base', 'bors_site');
config_set('project.name', 'bors_site');
config_set('classes_skip', array('storage_fs_xml', 'page_fs_xml'));
config_set('debug_hidden_log_dir', '/var/www/bors.balancer.ru/htdocs/logs');
config_set('default_template', 'default');
config_set('lcml_cache_disable', false);
config_set('obsolete_use_handlers_system', false);
config_set('page_fs_separate_cache_static', 86400);
config_set('page_fs_cache_static', 86400);
