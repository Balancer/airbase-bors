<?php

//config_set('default_template', 'bors:http://www.airbase.ru/cms/templates/skins/default/body/');
config_set('default_template', 'xfile:airbase/default/index2.html');
//config_set('default_template', 'xfile:forum/common.html');

config_set('sitemap_classes', 'balancer_board_topic');
config_set('lcml_old_keywords', true);

config_set('classes_auto_base', 'airbase');

config_set('cache_static.root', '/var/www/www.airbase.ru/htdocs/cache-static');

if(config('is_developer'))
{
//	config_set('debug_redirect_trace', true);
//	config_set('lcml_cache_disable', true);
}
