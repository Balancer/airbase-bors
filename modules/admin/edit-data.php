<?
    $page = @$GLOBALS['uri'];

//    exit( "Page = $edit_uri, action=$action");

    require_once("funcs/DataBaseHTS.php");
    require_once("funcs/users.php");
    require_once("funcs/navigation/go.php");

    $hts = new DataBaseHTS();
    $page = $hts->normalize_uri($page);

    foreach(split(' ','copyright create_time cr_type description_source flags nav_name split_type template title type') as $p)
        $$p = htmlspecialchars($hts->get_data($page,$p));

	if(!$create_time)
		$create_time = time();

  	if(empty($nav_name))
       	$nav_name = strtolower($title);

	access_warn($page, $hts);
?>
<table cellSpacing="0" class="btab">
<form method=POST action="<?echo $page?>?edit-data-save">
<tr><th align=right>Page title</th><td><input name="title" size="64" value="<?echo $title;?>"></td></tr>
<tr><th align=right>Description</th><td><textarea cols="32" rows="4" name="description"><?echo $description_source;?></textarea></td></tr>
<!--<tr><th align=right>Authors</th><td><input name="copyright" size="64" value="<?echo $copyright;?>"></td></tr>-->
<?/*
<tr><th align=right>Тип кода текста</th><td>
<select name="type">
<option value="text"<?if($type=='text')echo' selected'?>>Простой текст
<option value="html"<?if($type=='html')echo' selected'?>>Простой HTML
<option value="hts"<?if(!$type || $type=='hts')echo' selected'?>>HTS+автокод
</select></td></tr>*/?>
<tr><th align=right><a href="/support/hts/cr_type/">Carrige return type</a></th><td>
<select name="cr_type">
<option value="empty_as_para"<?if($cr_type=='empty_as_para' || !$cr_type)echo' selected'?>>Empty line = paragraph
<option value="string_as_para"<?if($cr_type=='string_as_para')echo' selected'?>>Any string = paragraph
<option value="dblstring_as_para"<?if($cr_type=='dblstring_as_para')echo' selected'?>>Empty line = line break
<option value="save_cr"<?if($cr_type=='save_cr')echo' selected'?>>Save line breacks
<option value="ignore_cr"<?if($cr_type=='ignore_cr')echo' selected'?>>Ignore line breacks
<option value="plain_text"<?if($cr_type=='plain_text')echo' selected'?>>Document is simple text
</select></td></tr>
<?
/*<tr><th align=right><a href="/support/hts/split_type/">Тип нарезки страницы</a></th><td>
<select name="split_type">
<option value="auto"<?if($split_type=='auto' || !$split_type)echo' selected'?>>Автонарезка по параграфу
<option value="user"<?if($split_type=='user')echo' selected'?>>Нарезка по тэгу #page
<option value="none"<?if($split_type=='none')echo' selected'?>>Не делать нарезку
</select></td></tr>*/
?>
<input type="hidden" name="split_type" value="auto">
<tr><th align=right>Page template</th><td><input name="template" size="64" value="<?echo $template;?>"></td></tr>
<? /*
    $base_page_access = $hts->base_value('default_access_level', 3);
    
    $up_level = $hts->get_data($page,'access_level', $base_page_access, true, true);
    $real_level = $hts->get_data($page, 'access_level', $base_page_access, true);
    $level    = $hts->get_data($page,'access_level');
    
    if($real_level < user_data('level'))
    {
        echo "<tr><th align=right>Уровни доступа:<br><small>собст. ".($level?" = $level":"не задан").", родит. ".($up_level?" = $up_level":"не задан")."</th><td>";

        echo "<select name=\"access_level\">\n";
        echo "<option value=\"$level\">не менять</option>\n";
        if(user_data('level') >= 4 || ($up_level >= $real_level && $up_level <= user_data('level')))
            echo "<option value=\"\">сбросить (до родительского)</option>\n";
        $min = $real_level;
        $max = user_data('level');
        if(user_data('level') >= 4)
        {
            $min = 1;
            $max = 10;
        }
        for($i=$min; $i<=$max; $i++)
            echo "<option value=\"$i\"".(($level==$i)?' selected':'').">$i</option>\n";

        echo "</select>\n</td></tr>\n";
    }
    else
        echo "<input type=\"hidden\" name=\"access_level\" value=\"$level\">";
*/
?>
<input type="hidden" name="access_level" value="3">
<tr><th align=right>Navigation name</th><td><input name="nav_name" size="64" value="<?echo $nav_name;?>"></td></tr>
<tr><th align=right>Creation&nbsp;date/time <small><nobr>(YYYY-MM-DD&nbsp;HH:MM:SS)</nobr></small></nobr></th><td><input name="create_time" size="20" value="<?echo strftime("%Y-%m-%d %H:%M:%S", $create_time);?>">&nbsp;<input type="checkbox" name="create_time_changed">Save change</td></tr>
<tr><th align=right>Parent pages</th><td><textarea cols="32" rows="4" name="parents">
<?
    foreach($hts->get_data_array($page,'parent') as $p)
        echo "$p\n";
?>
</textarea></td></tr>
<input type="hidden" name="action" value="save">
<input type="hidden" name="page" value="<?echo $page?>">
<tr><td>&nbsp;</td><td><input type=submit value="Save"></td></tr>
</form>
</table>
<h3>Access level: <?echo user_data("level")?></h3>
