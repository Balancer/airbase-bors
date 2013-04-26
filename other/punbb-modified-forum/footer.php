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


$tpl_temp = trim(ob_get_contents());

if(!empty($GLOBALS['global_cache']))
	$GLOBALS['global_cache']->set($tpl_temp, 7200);
	
$tpl_main = str_replace('<pun_main>', $tpl_temp, $tpl_main);
ob_end_clean();
// END SUBST - <pun_main>


// START SUBST - <pun_footer>
ob_start();

?>

<div class="box" style="padding: 4px; font-size: 10pt; font-weight: bold;">Сайт работает на сервере <a href="http://www.etegro.com/">ETegro Technologies</a></div><br/>

<div id="brdfooter" class="block">
	<h2><span><?php echo $lang_common['Board footer'] ?></span></h2>
	<div class="box">
		<div class="inbox">
<?php

// If no footer style has been specified, we use the default (only copyright/debug info)
$footer_style = isset($footer_style) ? $footer_style : NULL;

if ($footer_style == 'viewforum' || $footer_style == 'viewtopic')
{
	echo "\n\t\t\t".'<div class="conl">'."\n";

	// Display the "Jump to" drop list
	if ($pun_config['o_quickjump'] == '1')
	{
		// Load cached quickjump
		@include PUN_ROOT.'cache/cache_quickjump_'.$pun_user['g_id'].'.php';
		if (!defined('PUN_QJ_LOADED'))
		{
			require_once 'include/cache.php';
			generate_quickjump_cache($pun_user['g_id']);
			require 'cache/cache_quickjump_'.$pun_user['g_id'].'.php';
		}
	}

	if ($footer_style == 'viewforum' && $is_admmod)
		echo "\t\t\t<p id=\"modcontrols\"><a href=\"{$pun_config['root_uri']}/moderate.php?fid=$forum_id&amp;p=$p\">".$lang_common['Moderate forum'].'</a></p>'."\n";
	else if ($footer_style == 'viewtopic' && $is_coordinator)
	{
		echo "\t\t\t<dl id=\"modcontrols\"><dt><strong>{$lang_topic['Mod controls']}</strong></dt><dd><a href=\"{$pun_config['root_uri']}/moderate.php?fid=$forum_id&amp;tid=$id&amp;p=$p\">".$lang_common['Delete posts'].'</a></dd>'."\n";
		echo "\t\t\t<dd><a href=\"{$pun_config['root_uri']}/moderate.php?fid=$forum_id&amp;move_topics=$id\">".$lang_common['Move topic'].'</a></dd>'."\n";

		if ($cur_topic['closed'] == '1')
			echo "\t\t\t<dd><a href=\"{$pun_config['root_uri']}/moderate.php?fid=$forum_id&amp;open=$id\">".$lang_common['Open topic'].'</a></dd>'."\n";
		else
			echo "\t\t\t<dd><a href=\"{$pun_config['root_uri']}/moderate.php?fid=$forum_id&amp;close=$id\">".$lang_common['Close topic'].'</a></dd>'."\n";

		if ($cur_topic['sticky'] == '1')
			echo "\t\t\t<dd><a href=\"{$pun_config['root_uri']}/moderate.php?fid=$forum_id&amp;unstick=$id\">".$lang_common['Unstick topic'].'</a></dd></dl>'."\n";
		else
			echo "\t\t\t<dd><a href=\"{$pun_config['root_uri']}/moderate.php?fid=$forum_id&amp;stick=$id\">".$lang_common['Stick topic'].'</a></dd></dl>'."\n";
	}

	echo "\t\t\t".'</div>'."\n";
}

	if (!$pun_user['is_guest'])
	{
		if ($footer_style == 'viewtopic')
		{
			echo "<dl class=\"conl\"><dd><a href=\"".class_load('balancer_board_topic', $id)->url($p)."\">Эта тема на новом движке</a></dd>";
			echo "<dd><a href=\"".class_load('forum_printable', $id)->url()."\">Версия для печати</a></dd>";
			echo "<dd><a href=\"http://www.balancer.ru/forum/tools/topic/{$id}/reload/\">Пересчитать тему и сбросить кеши</a></dd>";
			echo "</dl>";
		}

		echo "\n\t\t\t<dl id=\"searchlinks\" class=\"conl\">\n\t\t\t\t<dt><strong>{$lang_common['Search links']}</strong></dt>\n\t\t\t\t<dd><a href=\"{$pun_config['root_uri']}/search.php?action=show_24h\">".$lang_common['Show recent posts'].'</a></dd>'."\n";
		echo "\t\t\t\t<dd><a href=\"{$pun_config['root_uri']}/search.php?action=show_unanswered\">".$lang_common['Show unanswered posts'].'</a></dd>'."\n";

		if ($pun_config['o_subscriptions'] == '1')
			echo "\t\t\t\t<dd><a href=\"{$pun_config['root_uri']}/search.php?action=show_subscriptions\">".$lang_common['Show subscriptions'].'</a></dd>'."\n";

		echo "\t\t\t\t<dd><a href=\"http://www.balancer.ru/user/{$pun_user['id']}/use-topics.html\">".$lang_common['Show your posts'].'</a></dd>'."\n\t\t\t".'</dl>'."\n";
	}
	else
	{
		if ($pun_user['g_search'] == '1')
		{
			echo "\n\t\t\t<dl id=\"searchlinks\" class=\"conl\">\n\t\t\t\t<dt><strong>{$lang_common['Search links']}</strong></dt><dd><a href=\"{$pun_config['root_uri']}/search.php?action=show_24h\">".$lang_common['Show recent posts'].'</a></dd>'."\n";
			echo "\t\t\t\t<dd><a href=\"{$pun_config['root_uri']}/search.php?action=show_unanswered\">".$lang_common['Show unanswered posts'].'</a></dd>'."\n\t\t\t".'</dl>'."\n";
		}
	}

?>
<a href="http://whos.amung.us/show/qz1y4sp9"><img src="http://whos.amung.us/swidget/qz1y4sp9.png" alt="website counter" width="80" height="15" border="0" /></a>

<!--Rating@Mail.ru COUNTER--><script language="JavaScript" type="text/javascript"><!--
d=document;var a='';a+=';r='+escape(d.referrer)
js=10//--></script><script language="JavaScript1.1" type="text/javascript"><!--
a+=';j='+navigator.javaEnabled()
js=11//--></script><script language="JavaScript1.2" type="text/javascript"><!--
s=screen;a+=';s='+s.width+'*'+s.height
a+=';d='+(s.colorDepth?s.colorDepth:s.pixelDepth)
js=12//--></script><script language="JavaScript1.3" type="text/javascript"><!--
js=13//--></script><script language="JavaScript" type="text/javascript"><!--
d.write('<a href="http://top.mail.ru/jump?from=1574967"'+
' target="_top"><img src="http://d8.c0.b8.a1.top.mail.ru/counter'+
'?id=1574967;t=57;js='+js+a+';rand='+Math.random()+
'" alt="Рейтинг@Mail.ru"'+' border="0" height="31" width="88"/><\/a>')
if(11<js)d.write('<'+'!-- ')//--></script><noscript><a
target="_top" href="http://top.mail.ru/jump?from=1574967"><img
src="http://d8.c0.b8.a1.top.mail.ru/counter?js=na;id=1574967;t=57"
border="0" height="31" width="88"
alt="Рейтинг@Mail.ru"/></a></noscript><script language="JavaScript" type="text/javascript"><!--
if(11<js)d.write('--'+'>')//--></script><!--/COUNTER-->

<a href="http://top.airbase.ru/"><img src="http://top.airbase.ru/?id=1" width="88" height="31" border="0" alt="АвиаТОП" title="Числа: место в рейтинге, хитов всего и хитов в сутки"></a>

<!-- Yandex.Metrika -->
<script src="//mc.yandex.ru/metrika/watch.js" type="text/javascript"></script>
<script type="text/javascript">
try { var yaCounter510488 = new Ya.Metrika(510488); } catch(e){}
</script>
<noscript><div style="position: absolute;"><img src="//mc.yandex.ru/watch/510488" alt="" /></div></noscript>
<!-- /Yandex.Metrika -->

<div class="clear">&nbsp;</div>

			<p class="conr">Powered by <a href="http://bors.balancer.ru">BORS(c) Framework</a> and modified <a href="http://www.punbb.org/">PunBB</a><?php if ($pun_config['o_show_version'] == '1') echo ' '.$pun_config['o_cur_version']; ?><br />
			&copy; Copyright 1998&#8211;<?php echo date('Y');?> <a href="https://plus.google.com/113730597040634449637?rel=author">Balancer</a><br />
			&copy; Copyright 2002&#8211;2005 Rickard Andersson</p>
<?php

// Display debug info (if enabled/defined)
if (defined('PUN_DEBUG'))
{
	// Calculate script generation time
	list($usec, $sec) = explode(' ', microtime());
	$time_diff = sprintf('%.3f', ((float)$usec + (float)$sec) - $pun_start);
//	echo "\t\t\t".'<p class="conr">[ Generated in '.$time_diff.' seconds, '.($db->get_num_queries()+debug_count('mysql_queries')).' queries executed ]</p>'."\n";
}

?>
			<div class="clearer"></div>
		</div>
	</div>
</div>
<?php


// End the transaction
//$db->end_transaction();

// Display executed queries (if enabled)
if (defined('PUN_SHOW_QUERIES'))
	display_saved_queries();

global $footer;
if(!empty($footer))
	foreach($footer as $s)
		echo $s;

$tpl_temp = trim(ob_get_contents());
$tpl_main = str_replace('<pun_footer>', $tpl_temp, $tpl_main);
ob_end_clean();
// END SUBST - <pun_footer>

// START SUBST - <pun_include "*">
while (preg_match('#<pun_include "([^/\\\\]*?)">#', $tpl_main, $cur_include))
{
	if (!file_exists(PUN_ROOT.'include/user/'.$cur_include[1]))
		error('Unable to process user include &lt;pun_include "'.htmlspecialchars($cur_include[1]).'"&gt; from template main.tpl. There is no such file in folder /include/user/');

	ob_start();
	include PUN_ROOT.'include/user/'.$cur_include[1];
	$tpl_temp = ob_get_contents();
	$tpl_main = str_replace($cur_include[0], $tpl_temp, $tpl_main);
    ob_end_clean();
}
// END SUBST - <pun_include "*">


// Close the db connection (and free up any result data)
$db->close();

// Spit out the page
pun_exit($tpl_main);
