<?php

config_set('timing_limit', 1);

require_once(__DIR__.'/config-host.php');

config_set('locked_db', @file_get_contents('/tmp/mysqldump.lock'));
if($fm = @filemtime('/tmp/mysqldump.lock'))
{
	config_set('locked_db_time', bors_lib_time::smart_interval_vp(time() - $fm));
	config_set('locked_db_message', @file_get_contents('/tmp/mysqldump-message.lock'));
}

function is_developer()
{
	return @$_COOKIE['user_id'] == 10000
		|| @$_COOKIE['user_id'] == 1615
//		|| @$_SERVER['REMOTE_ADDR'] == '95.31.43.16'
	;
}

//$message = ec(file_get_contents('/home/airbase/messages/lsbr-db-works.html'));
//echo $message; exit();

//if(!is_developer()) { header("Status: 302 Moved Temporarily"); header("Location: http://ls.balancer.ru/blog/airbase/95.html"); exit(); }

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
//config_set('memcached_tag', 38);

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

config_set('ims_enabled', true);

config_set('lcml_sharp_markup', true);
config_set('lcml_balancer', true);
config_set('lcml_smiles_cache_tag', '130520');
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

if(is_developer())
{
	config_set('is_developer', true);
	config_set('is_debug', true);
	config_set('debug.timing', true);
//	config_set('debug.trace_queries', true); // Это — вывод всех логов на экран
//	config_set('debug_redirect_trace', true);

//	var_dump($_GET); exit();
//	config_set('do_not_exit', true); 
//	config_set('cache_static', false);
//	config_set('debug_mysql_queries_log', 20);
	config_set('debug_mysql_queries_log', true); // — только строки запросов, без стека
//	config_set('strict_auto_fields_check', true);

//	config_set('cache_engine', 'bors_cache_redis');

//	config_set('debug_trace', true);
//	config_set('debug_trace_object_load', true);
//	config_set('debug_trace_object_load_trace', true);
//	config_set('debug_objects_create_counting_details', true);

//	config_set('topics.view_class', 'balancer_board_topics_view');
	config_set('user_overload_time', 1000);

	config_set('mysql.queries_watch_regexp', "!FROM bors_pictures_thumbs\s+ WHERE `id`='\d+,96x96!");
}

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

config_set('seo_domains_whitelist_regexp', '(www.balancer.ru|www.airbase.ru|www.bionco.ru|www.wrk.ru|www.tanzpol.org|www.aviaport.ru|balancer.endofinternet.net)$');

//config_set('balancer_board_domains', array('balancer.ru', 'airbase.ru', 'wrk.ru', 'tanzpol.org', 'bionco.ru'));
config_set('balancer_board_domains', array('balancer.ru', 'airbase.ru', 'wrk.ru', 'tanzpol.org'));

/*
if(config('is_developer'))
config_set('debug.mysql_queries_logs', array(
	'/SELECT posts.id,posts.title AS `title_raw`,posts.topic_id,posts.page AS `topic_page`/' => array('log' => '000-posts-cache'),
));
*/


config_set('lang.ua', array(
	'URL фида' => 'URL фіда',
	'Авиабаза' => 'Авіабаза',
	'авиабаза' => 'авіабаза',
	'Авиационные новости' => 'Авіаційні новини',
	'Администратор' => 'Адміністратор',
	'Админка' => 'Адмінпанель',
	'Аксакал' => 'Аксакал',
	'Бронетанковый' => 'Бронетанковий',
	'в начало страницы' => 'на початок сторінки',
	'Ваше в теме' => 'Ваше в темі',
	'Введите запрос' => 'Введіть запит',
	'вложение' => 'вкладення',
	'Втянувшийся' => 'Залучений',
	'вчера' => 'вчора',
	'Изменения в репутации автора за это сообщение' => 'Зміни в репутації автора за це повідомлення',
	'Изображение' => 'Зображення',
	'инструменты' => 'інструменти',
	'инфо' => 'інформація',
	': Информация' => ': Iнформація',
	'имитатор знатока' => 'імітатор знавця',
	'искать' => 'шукати',
	'Исторический' => 'Iсторичний',
	' кбайт' => ' кілобайт',
	'Клерк-старожил' => 'Клерк-старожил',
	'Комментарий' => 'Коментар',
	'Координатор' => 'Координатор',
	'Литератор' => 'Літератор',
	' Мбайт' => ' Мбайт',
	'Модератор' => 'Модератор',
	'Морской' => 'Морський',
	'Название' => 'Назва',
	'Найти в теме' => 'Знайти в темі',
	'Научно-технический' => 'Науково-технічний',
	'Начинающий' => 'Початківець',
	'Новости Авиабазы' => 'Новини Авіабази',
	'Новости ПРО' => 'Новини ПРО',
	'Настройки' => 'Налаштування',
	'Непосещённые темы' => 'Невідвідані теми',
	'нечитанное обновлённое' => 'нечитаю оновлене',
	'Новая тема' => 'Новий топік',
	'Новичок' => 'Новачок',
	'новое' => 'нове',
	'Новый опрос' => 'Нове голосування',
	'Обновившиеся темы' => 'Оновився теми',
	'обновления' => 'поновлення',
	'обновлённые темы' => 'оновлені теми',
	'Общевоенный' => 'Загальний військовий',
	'Объект ' => "Об'єкт ",
	'Описание' => 'Опис',
	'Ответ в эту тему' => 'Відповідь у цю тему',
	'Ответить на сообщение' => 'Відповісти на повідомлення',
	'оценки' => 'оцінки',
	'Оценки сообщений пользователя ' => 'Оцінки повідомлень користувача ',
	'Опытный' => 'Досвідчений',
	'Персональное' => 'Персональне',
	'По дате сообщения от новых' => 'За датою повідомлення від нових',
	'По дате сообщения от старых' => 'За датою повідомлення від старих',
	'По релевантности' => 'За релевантністю',
	'Поиск' => 'Пошук',
	'Поиск по запросу «' => 'Пошук за запитом «',
	'Политический' => 'Політичний',
	'пользователи' => 'користувачі',
	'Пользователи Balancer.ru' => 'Користувачі Balancer.ru',
	'Последние действия над темой' => 'Останні дії над темою',
	'Прикреплённые файлы:' => 'Приєднання:',
	'предупреждение' => 'попередження',
	'Просто юмор' => 'Просто гумор',
	'Радости жизни' => 'Радості життя',
	'Размер' => 'Розмір',
	'Расстояние до монитора' => 'Відстань до монітора',
	'Редактировать ' => 'Редагувати ',
	'Рельсовый, пассажирский и грузовой транспорт' => 'Рейковий, пасажирський і вантажний транспорт',
	'Репутация' => 'Репутація',
	'репутация' => 'репутація',
	'Россия' => 'Росія',
	'Сайт расходящихся тропок' => 'Сайт розходяться стежок',
	'Сальсолёт' => 'Сальсольот',
	'сегодня' => 'сьогодні',
	'Сейчас гостей' => 'Зараз гостей',
	'Сейчас зарегистрированных посетителей' => 'Зараз зареєстрованих відвідувачів',
	'Системные события' => 'Системні події',
	'См. также' => 'Дивись також',
	'Сообщение форума' => 'Повідомлення форуму',
	'сообщение форума' => 'повідомлення форуму',
	"Старые форумы Balancer'а" => "Старі форуми Balancer'а",
	'старые' => 'старі',
	'Статистика' => 'Статистика',
	'Старожил' => 'Старожил',
	'Страница' => 'Сторінка',
	'Страницы:' => 'Сторінки:',
	'Судомодельный' => 'Судномодельний',
	'Твиттер сайта' => 'Твіттер сайту',
	'Твиты пользователя' => 'Твіти користувача',
	'Теги' => 'Теги',
	'Теги       : ' => 'Теги       : ',
	'Тема с ограниченным доступом' => 'Тема обмежена доступу',
	'Тема закрыта' => 'Тема закрита',
	'темы с участием за месяц' => 'теми за участю за місяць',
	'Технологии и оборудование' => 'Технології та обладнання',
	'Украина и Крым' => 'Україна і Крим',
	'участник' => 'учасник',
	'Это сообщение редактировалось %s в %s' => 'Це повідомлення редагува %s в %s',
	'Форум' => 'Форум',
	'форумы' => 'форуми',
	'Форумы' => 'Форуми',
	"Форумы Balancer'а" => "Форуми Balancer'a",
	'Человек и общество' => 'Людина і суспільство',
	'Чертежи, фотографии и другая информация для судомоделистов' => 'Креслення, фотографії та інша інформація для судомоделістов',
	'Шрифт' => 'Шрифт',
	'Эксперт' => 'Eксперт',

	// Форумы
	'Авиационное видео' => 'Авіаційне відео',
	'Авиационные выставки и шоу' => 'Авіаційні виставки та шоу',
	'Авиационный' => 'Авіаційний',
	'Афганская война' => 'Афганська війна',
	'ИБА' => 'ВБА',
	'Клуб Героев города Жуковского' => 'Клуб Героїв міста Жуковського',
	'Комментарии' => 'Коментарі',
	'Радиоэлектронный' => 'Радіоелектронний',
	'ПВО' => 'ППО',
	'Прихожая' => 'Передпокій',
	'Сравнения авиатехники' => 'Порівняння авіатехніки',
	'Флейм и тесты' => 'Флейм і тести',

'Космический' => 'космічний',
'Игровой' => 'Ігровий',
'Компьютерный' => "Комп'ютерний",
'За жизнь' => 'за життя',
'Новости околоземной космонавтики' => 'Новини навколоземній космонавтики',
'Психология' => 'Психологія',
'Закрытый' => 'закритий',
'Рынок' => 'ринок',
'Ракетомодельный' => 'Ракетомодельний',
'Новости ПВО' => 'Новини ППО',
'Фантастика' => 'Фантастика',
'Тэги Авиабазы' => 'Теги Авіабази',
'Новости обо всём.' => 'Новини про все.',
'Международный' => 'Міжнародний',
'Израиль' => 'Ізраїль',
'Отстойник' => 'відстійник',
'Авиамодельный' => 'авіамодельний',
'Соционика' => 'соціоніка',
'Ледокол' => 'Криголам',
'Компьютерные новости' => "Комп'ютерні новини",
'Лунные космические программы' => 'Місячні космічні програми',
'Авиационные байки' => 'авіаційні байки',
'Новости экономики' => 'Новини економіки',
'Межпланетная космонавтика' => 'міжпланетна космонавтика',
'Исследования Марса' => 'дослідження Марса',
'Новости межпланетной космонавтики' => 'Новини міжпланетної космонавтики',
'Астрономия и дальний космос' => 'Астрономія і дальній космос',
'Альфа' => 'Альфа',
'Тропа Балансера' => 'Стежка Балансера',
'Велосипеды' => 'Велосипеди',
'Бета' => 'бета',
'Гамма' => 'гамма',
'Дельта' => 'Дельта',
'Типирование' => 'типування',
'Клуб Авиабазы' => 'клуб Авіабази',
'Астрономия и дальний космос' => 'Астрономія і дальній космос',
'Список оштрафованных пользователей' => 'Список оштрафованих користувачів',
'Правила Форумов Авиабазы' => 'Правила Форумів Авіабази',
'Террор' => 'терор',
'Персоналии форумов Авиабазы' => 'Персоналії форумів Авіабази',
'Пропаганда' => 'пропаганда',
'Архитектурный' => 'архітектурний',
'Московская любительская группа изучения реактивного движения (МосГИРД)' => 'Московська аматорська група вивчення реактивного руху (МосГІРД)',
'Автомобильный' => 'Автомобільний',
'ТИМы знаменитостей' => 'Тіми знаменитостей',
'Соционический юмор' => 'соціонічний гумор',
'Кино' => 'Кіно',
'ГИС' => 'ГІС',
'Компьютерный юмор' => "Комп'ютерний гумор",
'Искусство' => 'мистецтво',
'Linux и СПО' => 'Linux і СПО',
'Города и страны' => 'Міста і країни',
'Сон разума' => 'сон розуму',
'Музыка' => 'Музика',
'Знак качества' => 'знак якості',
'Рынок' => 'ринок',
'Отстойник' => 'відстійник',
'Общий' => 'Загальний',
'Вестник Адена' => 'вісник Адена',
'Общий клановый форум' => 'Загальний клановий форум',
'Внутренний' => 'внутрішній',
'Флейм' => 'Флейм',
'Рынок' => 'ринок',
'Баги' => 'баги',
'Новые идеи в L2J' => 'Нові ідеї в L2J',
'Мероприятия' => 'заходи',
'Техподдержка и связи с администрацией' => 'Техпідтримка зв\'язку з адміністрацією',
'Обсуждение альтернативных сборок' => 'Обговорення альтернативних збірок',
'Выполненные задания' => 'виконані завдання',
'Инструментарий' => 'інструментарій',
'Задания и вакансии сервера' => 'Завдання і вакансії сервера',
'Творчество игроков' => 'творчість гравців',
'Закрытый форум разработчиков' => 'Закритий форум розробників',
'Характеристики персонажей' => 'характеристики персонажів',
'Квесты и периодические мероприятия' => 'Квести і періодичні заходи',
'Открытый форум' => 'відкритий форум',
'Общение разработчиков' => 'Спілкування розробників',
'Переводы' => 'переклади',
'Помощь новичкам и вопросы по игре' => 'Допомога новачкам і питання по грі',
'Заявки на исправления' => 'Заявки на виправлення',
'Биологический' => 'біологічний',
'Химия для любознательных' => 'Хімія для допитливих',
'2006. Израиль-Ливан.' => '2006. Ізраїль - Ліван.',
'Документация и решения' => 'Документація та рішення',
'Лёгкая авиация' => 'Легка авіація',
'Просто гумор' => 'просто гумор',
'Техническая поддержка' => 'Технічна підтримка',
'Авиация Второй Мировой' => 'Авіація Другої Світової',
'Закрытый литературный' => 'закритий літературний',
'Новости Bionco.Ru' => 'Новини Bionco.Ru',
'Новости проекта L2Fortress' => 'Новини проекту L2Fortress',
'Прибалтика' => 'Прибалтика',
'Фото и видео' => 'Фото і відео',
'Программирование' => 'Програмування',
'Гуглоход' => 'Гуглоход',
'Игровой' => 'Ігровий',
'Вокруг Абхазии, Осетии, Грузии.' => 'Навколо Абхазії, Осетії, Грузії.',
'Новости грузинской войны' => 'Новини грузинської війни',
'Мировой кризис' => 'світова криза',
'Экономика' => 'Економіка',
'Чёрная дыра' => 'Чорна діра',
'Ретроэлектроника' => 'Ретроелектроніка',
'Imperion: Открытый форум альянсов A-Base и Единство' => 'Imperion: Відкритий форум альянсів A- Base і Єдність',
'Imperion. Закрытый форум альянса A-Base' => 'Imperion. Закритий форум альянсу A- Base',
'Imperion. Закрытый форум альянса Единство' => 'Imperion. Закритий форум альянсу Єдність',
'Военная авиация' => 'Військова авіація',
'Дайджест' => 'Дайджест',
'Спам' => 'спам',
'Мемориал' => 'меморіал',
'Новости проекта BORS©' => 'Новини проекту BORS©',
'Национальный и религиозный вопросы' => 'Національний і релігійний питання',
'СССР' => 'СРСР',
'Блоги и микроблоги' => 'Блоги та мікроблоги',
'Мобильные устройства и связь' => 'Мобільні пристрої і зв\'язок',
'Сотовая связь' => 'стільниковий зв\'язок',
'Пятое поколение' => 'п\'яте покоління',
'Бронемодельный' => 'Бронемодельний',
'Ленин и печник' => 'Ленін і пічник',
'А вы их дустом пробовали?' => 'А ви їх дустом пробували?',
'Научно-технические загадки' => 'Науково -технічні загадки',
'Новая (искусственная) жизнь L2F' => 'Нова (штучна) життя L2F',
'Современные международные конфликты' => 'Сучасні міжнародні конфлікти',

	// Категории
'Форумы Авиабазы' => 'Форуми Авіабази',
'Военспец' => 'военспец',
'Техподдержка сайта' => 'Техпідтримка сайту',
'Клуб' => 'клуб',
'Космос' => 'космос',
'соціоніка' => 'соціоніка',
'Наука и техника' => 'Наука і техніка',
'Моделизм и любительское ракетостроение' => 'Моделизм і аматорське ракетобудування',
'Теория и практика' => 'Теорія і практика',
'Квадры' => 'квадри',
'Кланы' => 'клани',
'Открытые форумы' => 'відкриті форуми',
'Закрытые форумы' => 'закриті форуми',
'Смотри также' => 'Дивись також',
'Прочее' => 'інше',
'Смотри также' => 'Дивись також',
'Bionco.Ru' => 'Bionco.Ru',
'Флот' => 'флот',
'Армия' => 'армія',

	// Звания
	'филин-стратег' => 'пугач-стратег',
	'Местный' => 'Місцевий',
	'Любитель тунисских тётков' => 'Любитель туніських тітка',

	// Персональное
	'Здравствуйте' => 'Привітання',
	'Выход' => 'Вихід',

	'Ваше' => 'Ваше',

	'Новое' => 'Нове',
	'Профили браузера' => 'Профілі браузера',
	'Блог' => 'Блог',
	'Избранное' => 'Вибране',
	'Темы с участием' => 'Теми з участю',
	'Сообщения' => 'Повідомлення',
	'Ответы Вам' => 'Відповіді Вам',
	'Репутация' => 'Репутація',
	'Оценки' => 'Оцінки',

	'Правила' => 'Правила',
	'Помощь' => 'Допомога',
	'За сутки' => 'За добу',
	'Чат' => 'Чат',
	'Теги' => 'Теги',
	'Репутации' => 'Репутації',
	'Штраф' => 'Штраф',
	'Штрафы' => 'Штрафи',
	'Инструменты' => 'Інструменти',
));
