<?
	// Основное назначение этого загрузчика - запуск PHP-кода
	// от пользователя - владельца файла в системах с suPHP.
	// Кидайте файл в каждый локальный проект и назначайте нужного юзера.

	// Путь, по которому лежит общий код BORS'а.
    define('BORS_INCLUDE', $_SERVER['DOCUMENT_ROOT'].'/../bors/');

	// Путь, где лежат локальные файлы проекта.
    define('BORS_INCLUDE_LOCAL', $_SERVER['DOCUMENT_ROOT'].'/../bors-local/');

	// Путь, где лежат файлы HTTP-кеша
	define('BORS_HTTP_CACHE_PATH', $_SERVER['DOCUMENT_ROOT'].'/');

	// URL предыдущего хранилища
	define('BORS_HTTP_CACHE_URL', 'http://'.$_SERVER['HTTP_HOST'].'/');

	// Путь к системному кешу (шаблоны Smarty и т.п.), может не совпадать с HTTP-кешем.
	define('BORS_SYSTEM_CACHE', $_SERVER['DOCUMENT_ROOT'].'/../cache/');

	include_once(BORS_INCLUDE.'main.php');
