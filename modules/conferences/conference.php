<?
    show_conference();

    function show_conference()
    {
	   	$conf = intval($GLOBALS['module_data']['conf']);

        $db = new DataBase();
?>
<table width="100%" cellspacing="0" cellpadding="3" border="0">
<tr>
  <td width="60%" nowrap="nowrap"  bgcolor="#FFFFFF"><font color='#000000' class="PhorumNav">&nbsp;<a href="post.php?f=1"><font color='#000000' class="PhorumNav">Новая тема</font></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="list.php?f=1"><font color='#000000' class="PhorumNav">В начало</font></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="search.php?f=1"><font color='#000000' class="PhorumNav">Поиск</font></a>&nbsp;</font></td>
  <td align="right" width="40%"  bgcolor="#FFFFFF"><div class="nav"><font color='#000000' class="PhorumNav">&nbsp;<a href="list.php?f=1&amp;t=928&amp;a=2"><font color='#000000' class="PhorumNav">Старые сообщения</font></a>&nbsp;</font></div></td>
</tr>
</table>
<table class="PhorumListTable" width="100%" cellspacing="1" cellpadding="2" border="0">
<tr>
    <td class="PhorumListHeader" bgcolor="#6699cc"><font color="#FFFFFF">&nbsp;Тема<img src="images/trans.gif" border="0" width="1" height="24" align="middle" alt="" /></font></td>
    <td class="PhorumListHeader" bgcolor="#6699cc" nowrap="nowrap" width="150"><font color="#FFFFFF">Автор&nbsp;</font></td>
    <td class="PhorumListHeader" bgcolor="#6699cc" nowrap="nowrap" width="150"><font color="#FFFFFF">Дата</font></td>
</tr>
<?    
		$db_names = array('1'=>'forum_news', '3'=>'forum_aviafirms', '4'=>'forum_sales');
		$db_name = $db_names[$conf];

//		$GLOBALS['log_level'] = 10;

		foreach($db->get_array("SELECT ftn.*, f.*, n.* FROM `$db_name` f LEFT JOIN `forums_id_to_news_id` ftn ON (f.thread = ftn.ThreadID) LEFT JOIN `News` n ON (n.ID=ftn.NewsID) GROUP BY f.thread ORDER BY `modifystamp` DESC LIMIT 0,25") as $r)
		{
			if($conf == 1 && preg_match("!^(\d{4})\-(\d{2})\-(\d{2})!", $r['Date'], $m))
				$link = "&nbsp;|&nbsp;<b><a href=\"/news/{$m[1]}/{$m[2]}/{$m[3]}/{$r['NewsID']}.html\">Обсуждаемая новость</a></b>";
			else
				$link = "";
			
			$date = strftime("%d-%m-%Y %H:%M", $db->get("SELECT `modifystamp` FROM `$db_name` ORDER BY `modifystamp` DESC LIMIT 1;"));
			
echo <<< __EOT__
	<tr valign="middle">
	<td><font color="#000000">&nbsp;<a href="/conferences/$conf/{$r['thread']}/">{$r['subject']}$link&nbsp;&nbsp;</font><font class="PhorumNewFlag"></font></td>
	<td class="PhorumListRow"  bgcolor="#ffffff" nowrap="nowrap"><font color="#000000">{$r['author']}</font></td>
	<td class="PhorumListRow"  bgcolor="#ffffff" nowrap="nowrap"><font color="#000000">$date&nbsp;</font></td>
	</tr>
__EOT__;
		}
?>
</table>
<?		
    }

?>
