<?php
/***********************************************************************

  Copyright (C) 2002-2005  Rickard Andersson (rickard@punbb.org)

  This file is part of PunBB.

  PunBB is free software; you can redistribute it and/or modify it
  under the terms of the GNU General Public License as published
  by the Free Software Foundation; either version 2 of the License,
  or (at your option) any later version.

  PunBB is distributed in the hope that it will be useful, but
  WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston,
  MA  02111-1307  USA

************************************************************************/


// Make sure no one attempts to run this script "directly"
if (!defined('PUN'))
	exit;

// Send no-cache headers
header('Expires: Thu, 21 Jul 1977 07:30:00 GMT');	// When yours truly first set eyes on this world! :)
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');		// For HTTP/1.0 compability


// Load the template
if (defined('PUN_ADMIN_CONSOLE'))
	$tpl_main = file_get_contents(PUN_ROOT.'include/template/admin.tpl');
else if (defined('PUN_HELP'))
	$tpl_main = file_get_contents(PUN_ROOT.'include/template/help.tpl');
else
	$tpl_main = file_get_contents(PUN_ROOT.'include/template/main.tpl');


// START SUBST - <pun_content_direction>
$tpl_main = str_replace('<pun_content_direction>', $lang_common['lang_direction'], $tpl_main);
// END SUBST - <pun_content_direction>


// START SUBST - <pun_char_encoding>
$tpl_main = str_replace('<pun_char_encoding>', $lang_common['lang_encoding'], $tpl_main);
// END SUBST - <pun_char_encoding>


// START SUBST - <pun_head>
ob_start();

// Is this a page that we want search index spiders to index?
//if (!defined('PUN_ALLOW_INDEX'))
	echo '<meta name="ROBOTS" content="NOINDEX, FOLLOW" />'."\n";
if(!empty($_GET['id']) && preg_match('/viewforum\.php/', $_SERVER['REQUEST_URI']))
	echo "<link rel=\"alternate\" type=\"application/rss+xml\" href=\"http://www.balancer.ru/forum/{$_GET['id']}/posts-rss.xml\" title=\"Новые сообщения в этом форуме\" />";

?>
<title><?php echo $page_title ?></title>
<meta name="Description" content="Форумы Balancer'а и Авиабазы. Свободное общение на всевозможные интересные темы. Военная и гражданская техника, авиация, космонавтика, компютеры и информационные технологии, Linux, люди, страны, политика, просто радости и горести жизни. У нас есть всё!">
<meta name="Keywords" content="форум, форумы, доска объявлений, авиабаза, люди, коллектив, клуб, сообщество, BORS, PHP, фреймворк, CMS, CMF, новости, мероприятия, авиация, видео, юмор, байки, космос, межпланетная космонавтика, ПВО, ПРО, флот, танки, наука, техника, радиоэлектроника, автомобили, метро, рельсовый транспорт, ракетостроение, ракетомоделизм, МосГИРД, Jabber, искусство, фантастика, города и страны, соционика, химия, биология">
<link rel="stylesheet" type="text/css" href="<?php echo $pun_config['root_uri'];?>/style/imports/colors.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $pun_config['root_uri'];?>/style/imports/fixes.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $pun_config['root_uri'];?>/style/<?php echo $pun_user['style'].'.css';/*"*/?>" />
<meta property="fb:admins" content="100000278666723" />
<?php
if(!empty($GLOBALS['use_jquery']))
	echo "<script src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js\" type=\"text/javascript\"></script>\n";
?>

<link rel="stylesheet" type="text/css" href="/_bors/css/bors/style.css" />
<link rel="stylesheet" type="text/css" href="/_bors/css/bors/code-geshi.css" />
<link rel="stylesheet" type="text/css" href="/_bal/css/main6.css" />
<?php
	global $header;
	if(!empty($header))
		foreach($header as $h)	
			echo $h;

if (defined('PUN_ADMIN_CONSOLE'))
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"{$pun_config['root_uri']}/style/imports/base_admin.css\" />\n";

if (isset($required_fields))
{
	// Output JavaScript to validate form (make sure required fields are filled out)

?>



<script type="text/javascript">
<!--
function process_form(the_form)
{
	var element_names = new Object()
<?php

	// Output a JavaScript array with localised field names
	while (list($elem_orig, $elem_trans) = @each($required_fields))
		echo "\t".'element_names["'.$elem_orig.'"] = "'.addslashes(str_replace('&nbsp;', ' ', $elem_trans)).'"'."\n";

?>

	if (document.all || document.getElementById)
	{
		for (i = 0; i < the_form.length; ++i)
		{
			var elem = the_form.elements[i]
			if (elem.name && elem.name.substring(0, 4) == "req_")
			{
				if (elem.type && (elem.type=="text" || elem.type=="xtextarea" || elem.type=="password" || elem.type=="file") && elem.value=='')
				{
					alert("\"" + element_names[elem.name] + "\" <?php echo $lang_common['required field'];/*"*/?>")
					elem.focus()
					return false
				}
			}
		}
	}

	return true
}
// -->
</script>
<?php

}

$user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : '';
if (strpos($user_agent, 'msie') !== false && strpos($user_agent, 'windows') !== false && strpos($user_agent, 'opera') === false)
	echo "<script type=\"text/javascript\" src=\"{$pun_config['root_uri']}/style/imports/minmax.js\"></script>";

$tpl_temp = trim(ob_get_contents());
$tpl_main = str_replace('<pun_head>', $tpl_temp, $tpl_main);
ob_end_clean();
// END SUBST - <pun_head>


// START SUBST - <body>
if (isset($focus_element))
{
	$tpl_main = str_replace('<body onload="', '<body onload="document.getElementById(\''.$focus_element[0].'\').'.$focus_element[1].'.focus();', $tpl_main);
	$tpl_main = str_replace('<body>', '<body onload="document.getElementById(\''.$focus_element[0].'\').'.$focus_element[1].'.focus()">', $tpl_main);
}
// END SUBST - <body>


// START SUBST - <pun_page>
$tpl_main = str_replace('<pun_page>', htmlspecialchars(basename($_SERVER['PHP_SELF'], '.php')), $tpl_main);
// END SUBST - <pun_title>


// START SUBST - <pun_title>
$tpl_main = str_replace('<pun_title>', '<h1><span>'.pun_htmlspecialchars($pun_config['o_board_title']).'</span></h1>', $tpl_main);
// END SUBST - <pun_title>


// START SUBST - <pun_desc>
$tpl_main = str_replace('<pun_desc>', '<p><span>'.$pun_config['o_board_desc'].'</span></p>', $tpl_main);
// END SUBST - <pun_desc>


// START SUBST - <pun_navlinks>
$tpl_main = str_replace('<pun_navlinks>','<div id="brdmenu" class="inbox">'."\n\t\t\t". generate_navlinks()."\n\t\t".'</div>', $tpl_main);
// END SUBST - <pun_navlinks>

//echo "pun_user['is_guest'] = {$pun_user['is_guest']}; pun_user['g_id'] = {$pun_user['g_id']}; PUN_GUEST = ".PUN_GUEST;

// START SUBST - <pun_status>
if ($pun_user['is_guest'])
	$tpl_temp = '<div id="brdwelcome" class="inbox">'."\n\t\t\t".'<p>'.$lang_common['Not logged in'].'</p>'."\n\t\t".'</div>';
else
{
	$tpl_temp = '<div id="brdwelcome" class="inbox">'."\n\t\t\t".'<ul class="conl">'."\n\t\t\t\t".'<li>'.$lang_common['Logged in as'].' <strong>'.pun_htmlspecialchars($pun_user['username']).'</strong></li>'."\n\t\t\t\t".'<li>'.$lang_common['Last visit'].': '.format_time($pun_user['last_visit']).'</li>';

	if ($pun_user['g_id'] < PUN_GUEST)
	{
		$result_header = $db->query('SELECT COUNT(id) FROM '.$db->prefix.'reports WHERE zapped IS NULL') or error('Unable to fetch reports info', __FILE__, __LINE__, $db->error());

		if ($db->result($result_header))
			$tpl_temp .= "\n\t\t\t\t<li class=\"reportlink\"><strong><a href=\"{$pun_config['root_uri']}/admin_reports.php\">There are new reports</a></strong></li>";

		if ($pun_config['o_maintenance'] == '1')
			$tpl_temp .= "\n\t\t\t\t<li class=\"maintenancelink\"><strong><a href=\"{$pun_config['root_uri']}/admin_options.php#maintenance\">Maintenance mode is enabled!</a></strong></li>";
	}

	$tpl_temp .= "</ul><ul class=\"conr\"><li>
 &middot; <a href=\"{$pun_config['root_uri']}/search.php?action=show_new\">{$lang_common['Show new posts']}</a><br />
<!-- &middot; <a href=\"{$pun_config['root_uri']}/search.php?action=show_24h\">{$lang_common['Show recent posts']}</a><br /> -->
 &middot; <a class=\"red\" href=\"http://forums.balancer.ru/personal/answers/\">Показать непрочитанные ответы на Ваши сообщения</a><br />
<!-- &middot; <a class=\"red\" href=\"http://www.balancer.ru/users/favorites/\">Ваше избранное</a><br /> -->
 &middot; <a href=\"http://www.balancer.ru/user/{$pun_user['id']}/use-topics.html\">Показать все темы с Вашим участием</a><br />
 	</li></ul>
	<div class=\"clearer\"></div>\n\t\t</div>";
}

$tpl_main = str_replace('<pun_status>', $tpl_temp, $tpl_main);
// END SUBST - <pun_status>


$tpl_announce = '';
if(false)
{
	ob_start();
?>
<div id="announce" class="block">
	<div class="box">
		<div class="inbox">
			<div style="font-size: 16px; color: red">Форумы на техобслуживании и поэтому временно в режиме «только для чтения».
			Подробности <a href="http://www.balancer.ru/pages/ro/">тут</a>.
			</div>
		</div>
	</div>
</div>
<?php
	$tpl_announce = trim(ob_get_contents());
	ob_end_clean();
}

// START SUBST - <pun_announcement>
if ($pun_config['o_announcement'] == '1')
{
	ob_start();

?>

<!-- script language="JavaScript1.2" src="http://www.balancer.ru/js/snow.js" script -->

<div id="announce" class="block">
	<h2><span><?php echo $lang_common['Announcement'] ?></span></h2>
	<div class="box">
		<div class="inbox">
			<div><?php echo $pun_config['o_announcement_message'] ?></div>
		</div>
	</div>

	<br/>
	<div class="box">
		<div class="inbox">
			<div style="font-size: 16px; color: red">Форумы на техобслуживании. Можно скоротать время во
				<b><a href="http://home.balancer.ru/lorduino/">временном чате</a></b>. Регистрации никакой не требуется.
			</div>
		</div>
	</div>

</div>

<?php

	$tpl_temp = trim(ob_get_contents());
	$tpl_main = str_replace('<pun_announcement>', $tpl_temp, $tpl_main);
	ob_end_clean();
}
else
{
	if($tpl_announce)
		$tpl_main = str_replace('<pun_announcement>', $tpl_announce, $tpl_main);

	$tpl_main = str_replace('<pun_announcement>', '', $tpl_main);
}
// END SUBST - <pun_announcement>


// START SUBST - <pun_main>
ob_start();

define('PUN_HEADER', 1);
