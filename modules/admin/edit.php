<?
    require_once("funcs/DataBaseHTS.php");

    if(empty($title) && !empty($htitle))
        $title = $htitle;
    
    foreach(split(' ','title ref description page uri nav_name') as $p)
        $$p = @$GLOBALS[$p];

/*
    �� ����� ���������� ���������� $uri � ������ ������� (URL) �������� ��������.
*/

    $hts = new DataBaseHTS();
    $uri = $hts->normalize_uri($uri);

    $new_page = (!$hts->get_data($uri,'title')) || (!$hts->get_data($uri,'create_time'));

//    if(empty($ref))
//        $ref = isset($_SERVER['HTTP_REFERER']) ? $GLOBALS['cms']['host_url'].$_SERVER['HTTP_REFERER'] : NULL;

//    if(preg_match("!/admin/edit!", $ref))
//        $ref = ''; //"http://airbase.ru/not_ref_pages/";

    $source = $hts->get_data($uri, 'source');

    if(empty($title))
        $title = $hts->get_data($uri, 'title');

?>
<form method="POST" action="<?echo $uri?>?edit-save" ENCTYPE="multipart/form-data">
<!-- <h1><?echo ($new_page && empty($title))?'����� ��������':$title;?></h1> -->
<?
    
    
    if($new_page)
    {
        echo "<table class=\"btab\" cellSpacing=\"0\">";
        if(!$title) echo "<tr><th><b>��������:</b></th><td><input name=\"title\" value=\"\" size=\"50\" maxlen=\"255\" /></td></tr>\n";
        echo "<tr><th><b>�������� ��� ���������:</b></th><td><input name=\"nav_name\" size=\"50\" maxlen=\"255\" value=\"".strtolower($title)."\"/></td></tr>\n";
        echo "<tr><th><b>������� ��������:</b></th><td><textarea name=\"description\" cols=\"32\" rows=\"3\" /></textarea></td></tr>\n";
	    if($ref)   echo "<input type=\"hidden\" name=\"ref\"    value=\"".addslashes($ref)  ."\">\n";
    }
	else
	{
    	if($title) echo "<input type=\"hidden\" name=\"title\" 	value=\"".addslashes($title)."\">\n";
	}
?>

<table cellSpacing="0" class="btab">
<?
	if(access_warn($new_page ? $ref : $uri, $hts))
		echo "<tr><td>�����: <input name=\"login\"></td><td>������: <input name=\"password\" type=\"password\"></td></tr>";
?>
<tr><td colSpan="2"><textarea cols="64" rows="25" name="source"><?echo htmlspecialchars("$source")?></textarea></td></tr>
<tr><td>&nbsp;</td><td>
<input type="submit" value="���������"></td></tr>
<input type="hidden" name="action" value="save">
<input type="hidden" name="page"  value="<?echo $uri?>">
<?
    if($uri)   echo "<input type=\"hidden\" name=\"uri\"    value=\"".addslashes($uri)  ."\">\n";
?>
</table>
</form>
<i>������� �������: <?echo user_data("level",NULL,1)?></i><br />
