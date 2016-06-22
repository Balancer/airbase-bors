<?php

config_set('timing_limit', 1);

require_once(COMPOSER_ROOT.'/config-host.php');

config_set('locked_db', @file_get_contents('/tmp/mysqldump.lock'));
if($fm = @filemtime('/tmp/mysqldump.lock'))
{
	config_set('locked_db_time', bors_lib_time::smart_interval_vp(time() - $fm));
	config_set('locked_db_message', @file_get_contents('/tmp/mysqldump-message.lock'));
}

//$message = ec(file_get_contents('/home/airbase/messages/lsbr-db-works.html'));
//echo $message; exit();


// header("Status: 302 Moved Temporarily"); header("Location: http://home.balancer.ru/mybb/Thread-%D0%9D%D0%B0-%D1%81%D0%B5%D1%80%D0%B2%D0%B5%D1%80%D0%B5-%D0%90%D0%B2%D0%B8%D0%B0%D0%B1%D0%B0%D0%B7%D1%8B-%D0%BF%D1%80%D0%BE%D0%B2%D0%BE%D0%B4%D1%8F%D1%82%D1%81%D1%8F-%D1%82%D0%B5%D1%85%D0%BD%D0%B8%D1%87%D0%B5%D1%81%D0%BA%D0%B8%D0%B5-%D1%80%D0%B0%D0%B1%D0%BE%D1%82%D1%8B"); exit();

// header("Status: 302 Moved Temporarily"); header("Location: http://home.balancer.ru/mybb/Thread-Проблемы-с-сервером?pid=38864#pid38864"); exit();

//header("Status: 302 Moved Temporarily");
// На сервере Авиабазы проводятся технические работы
//header("Location: http://balancer.endofinternet.net/mybb/Thread-На-сервере-Авиабазы-проводятся-технические-работы");
//exit();

config_set('mysql_disable_autoselect_db', true);
config_set('mysql_use_pool2', true);
config_set('mysql_try_reconnect', 0);

config_set('mysql_set_names_charset', 'utf8mb4');

//Протестировать на примере переноса сообщений из темы в тему. Не обновляется new_topic_id
//config_set('memcached', 'localhost');
//config_set('memcached_tag', 39);

config_set('main_bors_db', 'AB_BORS');
config_set('bors_core_db', 'AB_BORS');
config_set('rss_static_lifetime', '1200');
config_set('bot_lavg_limit', 12);

config_set('access_log', true);
config_set('access_default', 'balancer_board_access_public');
config_set('admin_access_default', 'airbase_access_balancer');

//config_set('cache_dir', '/var/www/balancer.ru/htdocs/cache/system');
//config_set('cache_engine', 'cache_smart');
config_set('cache_engine', 'bors_cache_redis');
config_set('cache_fast_engine', 'bors_cache_redis');

config_set('cache_stat_dirs', array(
	'/var/www/balancer.ru/htdocs',
));

config_set('forum_attach_max_size', 5000000);

config_set('user_overload_time', 700);
config_set('bot_overload_time', 500);

config_set('default_events_class', 'bal_event');

config_set('user_class', 'balancer_board_user');
config_set('user_engine', 'balancer_board_user'); //TODO: поменять на user_class
config_set('user_engine_old', 'punbb');
//config_set('smarty_path', 'smarty');
config_set('smarty3_enable', true);
config_set('mysql_database', 'HTS');
config_set('cache_database', 'CACHE');
config_set('cache_static', true);
config_set('static_forum', false);

config_set('cache_static.root', @$_SERVER['DOCUMENT_ROOT'].'/cache-static');

config_set('search_db', 'SEARCH');

config_set('system.use_sessions', true);

config_set('forums_private', array(19, 37, 102, 138, 170));

config_set('sites_store_path', '/var/www/balancer.ru/htdocs/sites');
config_set('sites_store_url', 'http://www.balancer.ru/sites');

config_set('main_host_dir', '/var/www/balancer.ru/htdocs');
config_set('main_host_url', 'http://www.balancer.ru');

config_set('images_resize_max_width', 16384);
config_set('images_resize_max_height', 16384);
config_set('images_resize_max_area', 31*1024*1024);
config_set('images_resize_filesize_enabled', 1024*1024);

config_set('image_transform_engine', 'Imagick3');

config_set('pics_base_url', 'http://www.balancer.ru');

config_set('ims_enabled', true);

config_set('lcml_sharp_markup', true);
config_set('lcml_balancer', true);
config_set('lcml_smiles_cache_tag', '150320-2');
config_set('lcml_old_exclamation_heads', true);

config_set('ref_count_skip_domains', explode(' ', 'admin.airbase.ru www.airbase.ru www.balancer.ru www.bionco.ru blogs.balancer.ru bors.balancer.ru bp.wrk.ru files.balancer.ru flerpark.ru forums.airbase.ru forums.balancer.ru fotocollag.ru gorod-spal.ru epizodsspace.airbase.ru karshiev.ru karshieva.ru la2.balancer.ru la2.wrk.ru m.balancer.ru navy.balancer.ru photoburo.ru sologubov.ru tanzpol.org www.tanzpol.org top.airbase.ru trac.balancer.ru wiki.airbase.ru wrk.ru www.bionco.ru www.bp.wrk.ru www.photoburo.ru'));
config_set('ref_count_skip_target_domains', explode(' ', 'epizodsspace.airbase.ru'));
config_set('ref_count_skip_target_regexp', '(http://www.airbase.ru/top/.*)');

config_set('search_sphinx_host', 'localhost');
config_set('search_sphinx_port', 3312);
config_set('search_sphinx_max_matches', 1000);

config_set('smilies_dir', '/var/www/airbase.ru/htdocs/forum/smilies');
config_set('smilies_url', 'http://s.wrk.ru/s');

config_set('overload_time', 40);
config_set('user_overload_time', 207);
config_set('bot_overload_time', 40);

$GLOBALS['echofile'] = "/var/www/balancer.ru/htdocs/logs/echolog.log";

config_set('404_show', true);
config_set('obsolete_use_handlers_system', true);

//config_set('default_template', 'forum/common.html');
//config_set('default_template', 'bors:http://www.airbase.ru/cms/templates/skins/default/body/');
//config_set('default_template', 'xfile:airbase/default/index2.html');
config_set('default_template', 'xfile:forum/common.html');

// Debug
//config_set('debug_class_search_track', true);

config_set('timing_log', '/var/www/balancer.ru/htdocs/logs/timing.log');
config_set('debug_hidden_log_dir', '/var/www/balancer.ru/htdocs/logs');

config_set('object_loader_filemtime_check', false);

define('WARNING_DAYS', 14);


if(in_array(@$_SERVER['REMOTE_ADDR'], array(
    '92.113.69.79',
//    '72.30.79.34', // Yahoo
	'94.127.144.35', // Mozilla/5.0 (compatible; Dolphin/1.0; +http://tele-house.ru/crawler.html)
	'91.205.124.13', // Yanga WorldSearch Bot v1.1/beta (http://www.yanga.co.uk/)
	'89.102.199.235', // Чешский бот, грузящий систему. Юзер 39081, Standa. Он же - 89.102.199.235/Cmolda
	'211.100.62.157', // Что-то из Китая. Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; GTB6; User-agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; http://bsalsa.com) ; MAXTHON 2.0)
	'211.100.62.152', // ^^ выше
	'113.107.72.104', // uid = 29969, китайский бот?
	'116.7.56.221', // Китайский бот, 4947 обращений за 15 минут
	'183.13.247.173', // Китай, 3888 за 15 минут.
	'95.221.164.70', // Сильно перегрузил сайт после рестарта
    )))
{
    header('Status: 503 Service Temporarily Unavailable');
    header('Retry-After: 600');

    @file_put_contents($file = $_SERVER['DOCUMENT_ROOT']."/logs/ip-bans.log",
        date('Y.m.d H:i:s ')
            .$_SERVER['REQUEST_URI']
            ."; ref=".@$_SERVER['HTTP_REFERER']
            . "; IP=".@$_SERVER['REMOTE_ADDR']
            ."; UA=".@$_SERVER['HTTP_USER_AGENT']
            ."; LA={$load_avg}\n", FILE_APPEND);
    @chmod($file, 0666);

    exit("Service Temporarily Unavailable");
}

config_set('topics.view_class', 'balancer_board_topic');

config_set('error_message_header', "
<h1>Внимание! Произошла системная ошибка!</h1>
Вполне может быть, что сейчас система уже восстанавливается. Хотя в некоторых случаях это
может занять долгое время. Пока же можете посетить:
<ul>
<li><a href=\"http://home.balancer.ru/lorduino/\" style=\"color: red; font-weight: bold\">Чат Авиабазы</a> (не требуется никакой регистрации)</li>
<li><a href=\"http://ls.balancer.ru/\">LSBR</a> (там работает форумная авторизация, те же логин с паролем, что и на форумах)</li>
<li>Если не работает и LSBR, попробуйте <a href=\"http://home.balancer.ru/mybb/\">ЗАПАСНОЙ ФОРУМ</a></li>
</ul>
Ниже располагается служебная информация об ошибке. Можете не обращать на неё внимание.<br/><br/><br/><hr/>
");

register_vhost('www.airbase.ru', NULL, '/var/www/airbase.ru/bors-site');
register_vhost('www.airbase.ru:8080', '/var/lib/tomcat-6/webapps');
register_vhost('www.balancer.ru', NULL, '/var/www/balancer.ru/bors-site');
register_vhost('www.bionco.ru', NULL, '/var/www/bionco.ru/bors-site');
register_vhost('files.balancer.ru', '/var/www/files.balancer.ru/files');
register_vhost('forums.airbase.ru', NULL, '/var/www/forums.airbase.ru/bors-site');
register_vhost('forums.balancer.ru', NULL, '/var/www/forums.balancer.ru/bors-site');
register_vhost('games.balancer.ru');
register_vhost('la2.balancer.ru', NULL, '/var/www/la2.balancer.ru/.bors-host');
register_vhost('la2.wrk.ru');
register_vhost('mail.balancer.ru');
register_vhost('navy.balancer.ru', NULL, '/var/www/navy.balancer.ru/bors-host');
register_vhost('top.airbase.ru');
register_vhost('s.wrk.ru');
register_vhost('www.wrk.ru');
register_vhost('www.tanzpol.org');

config_set('seo_domains_whitelist_regexp', '(www.balancer.ru|www.airbase.ru|www.bionco.ru|www.wrk.ru|www.tanzpol.org|www.aviaport.ru|balancer.endofinternet.net)$');

//config_set('balancer_board_domains', array('balancer.ru', 'airbase.ru', 'wrk.ru', 'tanzpol.org', 'bionco.ru'));
config_set('balancer_board_domains', array('balancer.ru', 'airbase.ru', 'wrk.ru', 'tanzpol.org'));

/*
if(config('is_developer'))
config_set('debug.mysql_queries_logs', array(
	'/SELECT posts.id,posts.title AS `title_raw`,posts.topic_id,posts.page AS `topic_page`/' => array('log' => '000-posts-cache'),
));
*/

bors_config_ini(COMPOSER_ROOT.'/config-host.ini');

require_once(__DIR__.'/config-post.inc.php');
