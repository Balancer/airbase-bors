<?
    show_conferences();

    function show_conferences()
    {
        $db = new DataBase();
?>
<table width="100%" summary="layout" cellpadding="0" cellspacing="0">
<tr>
<td id="pannel-center-main">
<table class="PhorumListTable" width="100%" cellspacing="0" cellpadding="2" border="0">
<tr>
    <td class="PhorumTableHeader" width="100%" colspan="3"  bgcolor="#6699cc"><font color="#FFFFFF">&nbsp;</font><br /></td>
</tr>
<?    
		$cdb_name = array('1'=>'forum_news', '3'=>'forum_aviafirms', '4'=>'forum_sales');

		foreach($db->get_array("SELECT * FROM `forums`") as $r)
		{
			$db_name = $cdb_name[$r['id']];
			
			$count = $db->get("SELECT COUNT(*) FROM `$db_name`;");
//			$GLOBALS['log_level'] = 10;
			$last = strftime("%d-%m-%Y %H:%M", $db->get("SELECT `modifystamp` FROM `$db_name` ORDER BY `modifystamp` DESC LIMIT 1;"));
//			$GLOBALS['log_level'] = 2;
?>
<tr>
  <td nowrap="nowrap" bgcolor="#ffffff"><font color="#000000" class="PhorumForumTitle">&nbsp;<a href="/conferences/<?echo $r['id']?>/"><?echo $r['name']?></a></font><br /><br /></td>
  <td nowrap="nowrap" bgcolor="#ffffff"><font color="#000000">&nbsp;&nbsp;Сообщения: <strong><?echo $count?></strong>&nbsp;&nbsp;</font></td>
  <td nowrap="nowrap" bgcolor="#ffffff"><font color="#000000">&nbsp;&nbsp;Последнее сообщение: <strong><?echo $last?></strong></font></td>
</tr>
<?
		}
?>
</table>
<?		
    }

?>
