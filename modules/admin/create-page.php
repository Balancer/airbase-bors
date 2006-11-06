<?
    require_once("funcs/DataBaseHTS.php");

/*
    На входе глобальная переменная $uri с полным адресом (URL) новой реальной страницы.
*/

	foreach(split(' ', 'ref title uri') as $p)
		$$p = @$GLOBALS[$p];

	if(!$ref)
		$ref = preg_replace("!(\?.*?)$!","",@$_SERVER['HTTP_REFERER']);

	$ref = preg_replace("!/[^/]+\.html$!", "/", $ref);

    $hts = new DataBaseHTS();
    $uri = $hts->normalize_uri($uri);

	if(!$uri)
	{
		echo "<h3>Empty uri!</h3>";
		return;
	}

	$source = $hts->get_data($uri, 'source');

?>
<form method="post" action="<?echo $uri?>?create-page" ENCTYPE="multipart/form-data">
<h1><?echo $title?$title:ec('Новая страница');?></h1>
<table class="btab" cellSpacing="0">
<?
	if(access_warn($ref, $hts))
		echo ec("<tr><td>Логин:&nbsp;<input name=\"login\"></td><td>Пароль:&nbsp;<input name=\"password\" type=\"password\"></td></tr>");

    if(!$title) 
		echo ec("<tr><th><b>Название:</b></th><td><input name=\"title\" value=\"\" size=\"50\" maxlen=\"255\" /></td></tr>\n");
	else
    	echo "<input type=\"hidden\" name=\"title\" 	value=\"".addslashes($title)."\">\n";
    echo ec("<tr><th><b>Название для навигации:</b></th><td><input name=\"nav_name\" size=\"50\" maxlen=\"255\" value=\"").strtolower($title)."\"/></td></tr>\n";
    echo ec("<tr><th><b>Краткое описание:</b></th><td><textarea name=\"description\" cols=\"32\" rows=\"3\" /></textarea></td></tr>\n");
?>
<tr><td colSpan="2"><textarea cols="64" rows="25" name="source"><?echo htmlspecialchars("$source")?></textarea></td></tr>
<tr><td>&nbsp;</td><td>
<input type="submit" value="<? echo ec("Создать");?>"></td></tr>
<?
    if($ref)   echo "<input type=\"hidden\" name=\"ref\"    value=\"".addslashes($ref)  ."\">\n";
    if($uri)   echo "<input type=\"hidden\" name=\"uri\"    value=\"".addslashes($uri)  ."\">\n";
?>
</table>
<input type="hidden" name="create-page" value="1">
</form>
<h3><?echo ec("Уровень доступа");?>: <?echo user_data("level")?></h3>
