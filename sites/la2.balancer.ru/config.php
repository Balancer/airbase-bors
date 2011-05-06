<?php

config_set('lcml_cache_disable', true); 

mysql_access('l2jdb', 'la2', 'la2kkk');

config_set('default_template', 'bors:http://la2.balancer.ru/cms/templates/default/');
config_set('sitemap_classes', 'balancer_board_topic');

register_vhost('airbase.ru', NULL, '/var/www/airbase.ru/.bors-host');
register_vhost('airbase.ru:8080', '/var/lib/tomcat-6/webapps');
register_vhost('balancer.ru', NULL, '/var/www/balancer.ru/.bors-host');
register_vhost('bionco.ru', NULL, '/var/www/bionco.ru/bors-site');
register_vhost('files.balancer.ru', '/var/www/files.balancer.ru/files');
register_vhost('forums.airbase.ru', NULL, '/var/www/forums.airbase.ru/.bors-host');
register_vhost('forums.balancer.ru', NULL, '/var/www/forums.balancer.ru/bors-host');
register_vhost('games.balancer.ru');
register_vhost('la2.balancer.ru', NULL, '/var/www/la2.balancer.ru/.bors-host');
register_vhost('la2.wrk.ru');
register_vhost('mail.balancer.ru');
register_vhost('navy.balancer.ru', NULL, '/var/www/navy.balancer.ru/bors-host');
register_vhost('top.airbase.ru');
register_vhost('wrk.ru');
