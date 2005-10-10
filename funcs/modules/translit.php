<?
function from_translit($s)
{
    $s=str_replace("shch","",$s);
    $s=str_replace("Shch","",$s);
    $s=str_replace("SHCH","",$s);
    $s=str_replace("jo","",$s);
    $s=str_replace("Jo","",$s);
    $s=str_replace("JO","",$s);
    $s=str_replace("yo","",$s);
    $s=str_replace("Yo","",$s);
    $s=str_replace("YO","",$s);
    $s=str_replace("yu","",$s);
    $s=str_replace("Yu","",$s);
    $s=str_replace("YU","",$s);
    $s=str_replace("ya","",$s);
    $s=str_replace("Ya","",$s);
    $s=str_replace("YA","",$s);
    $s=str_replace("zh","",$s);
    $s=str_replace("Zh","",$s);
    $s=str_replace("ZH","",$s);
    $s=str_replace("kh","",$s);
    $s=str_replace("Kh","",$s);
    $s=str_replace("KH","",$s);
    $s=str_replace("ch","",$s);
    $s=str_replace("Ch","",$s);
    $s=str_replace("CH","",$s);
    $s=str_replace("sh","",$s);
    $s=str_replace("Sh","",$s);
    $s=str_replace("SH","",$s);
    $s=str_replace("e\'","",$s);
    $s=str_replace("e&#39;","",$s);
    $s=str_replace("E\'","",$s);
    $s=str_replace("E&#39;","",$s);
    $s=str_replace("ju","",$s);
    $s=str_replace("Ju","",$s);
    $s=str_replace("JU","",$s);
    $s=str_replace("ja","",$s);
    $s=str_replace("Ja","",$s);
    $s=str_replace("JA","",$s);
    $from="abwvgdezijklmnoprstufhc'yABWVGDEZIJKLMNOPRSTUFhC'Y";
      $to="";

    for($i=0;$i<strlen($from);$i++)
        $s=str_replace($from[$i],$to[$i],$s);

    return $s;
}

    function to_translit($s)
    {   
        $from=array('а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я','А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я');
        $to=array('a','b','v','g','d','e','yo','zh','z','i','j','k','l','m','n','o','p','r','s','t','u','f','kh','ts','ch','sh','sch','\'','y','\'','e','yu','ya','A','B','V','G','D','E','YO','ZH','Z','I','J','K','L','M','N','O','P','R','S','T','U','F','KH','TS','CH','SH','SCH','\'','Y','\'','E','YU','YA');
        return str_replace($from,$to,$s);
    }
?>
