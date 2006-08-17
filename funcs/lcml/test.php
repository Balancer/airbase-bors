<?

//yyyyyyyyyyyyyyyy

$_SERVER['DOCUMENT_ROOT'] = "/var/www/bal.aviaport.ru/htdocs";
$_SERVER['HTTP_HOST'] = "balancer.ru";
$GLOBALS['cms']['page_path'] = $GLOBALS['main_uri'] = "http://balancer.ru/";

require_once($_SERVER['DOCUMENT_ROOT'].'/cms/config.php');
require_once('funcs/lcml.php');
require_once('funcs/global-data.php');

$GLOBALS['cms']['cache_disabled'] = true;
$GLOBALS['cms']['templates_cache_disabled'] = true;

$txt=<<<EOT
=====================
== Test ==
<h4> aa
bb </h4>

<h4><a name="issue_01_01">Что такое избранные темы?</a></h4>
"Избранные темы" - это темы, которые пользователи сервиса "АвиаПорт.Конференции" считают важными и интересными. Каждый пользователь сервиса имеет возможность собирать свой <a href="#issue_03_02">список избранных тем</a>. На основе списков пользователей по специальному алгоритму формируется "<a href="http://www.aviaport.ru/conferences/favorites/" blank="_help">Избранные темы</a>" конференции.

<h4><a name="issue_01_02">Почему различается цвет даты/времени обновления у разных тем?</a></h4>
Цвет даты/времени обновления зависит от популярности темы у читателей (наиболее часто читаемая тема) и авторов (тема, собравшая наибольшее количество ответов) конференции.
<br /><img width="290" height="74" src="/images/image002.jpg">

=====================


EOT;

//exit(preg_replace("!(\s|^)(http://\S+?([^/]+\.mp3))(\s|$)!ie", "'$1=$2=$4'", $txt));


//require_once('funcs/lcml/post/00-tables.php');

echo lcml($txt, array(
				'cr_type' => 'empty_as_para',
				'forum_type' => 'punbb',
				'forum_base_uri' => 'http://balancer.ru/forum',
//				'sharp_not_comment' => true,
				'html_disable' => false,
				'with_html' => true,
			));

?>
