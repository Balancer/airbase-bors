<?
function from_translit($s)
{
    $s=str_replace("shch","щ",$s);
    $s=str_replace("Shch","Щ",$s);
    $s=str_replace("SHCH","Щ",$s);
    $s=str_replace("jo","ё",$s);
    $s=str_replace("Jo","Ё",$s);
    $s=str_replace("JO","Ё",$s);
    $s=str_replace("je","е",$s);
    $s=str_replace("Je","Е",$s);
    $s=str_replace("JE","Е",$s);
    $s=str_replace("yo","ё",$s);
    $s=str_replace("Yo","Ё",$s);
    $s=str_replace("YO","Ё",$s);
    $s=str_replace("yu","ю",$s);
    $s=str_replace("Yu","Ю",$s);
    $s=str_replace("YU","Ю",$s);
    $s=str_replace("ya","я",$s);
    $s=str_replace("Ya","Я",$s);
    $s=str_replace("YA","Я",$s);
    $s=str_replace("zh","ж",$s);
    $s=str_replace("Zh","Ж",$s);
    $s=str_replace("ZH","Ж",$s);
    $s=str_replace("kh","х",$s);
    $s=str_replace("Kh","Х",$s);
    $s=str_replace("KH","Х",$s);
    $s=str_replace("ch","ч",$s);
    $s=str_replace("Ch","Ч",$s);
    $s=str_replace("CH","Ч",$s);
    $s=str_replace("sh","ш",$s);
    $s=str_replace("Sh","Ш",$s);
    $s=str_replace("SH","Ш",$s);
    $s=str_replace("e\'","э",$s);
    $s=str_replace("e&#39;","э",$s);
    $s=str_replace("E\'","Э",$s);
    $s=str_replace("E&#39;","Э",$s);
    $s=str_replace("ju","ю",$s);
    $s=str_replace("Ju","Ю",$s);
    $s=str_replace("JU","Ю",$s);
    $s=str_replace("ja","я",$s);
    $s=str_replace("Ja","Я",$s);
    $s=str_replace("JA","Я",$s);
    $s=str_replace("ts","ц",$s);
    $s=str_replace("Ts","Ц",$s);
    $s=str_replace("TS","Ц",$s);

//    $from="abwvgdezijklmnoprstufhc'yABWVGDEZIJKLMNOPRSTUFHC'Y";
//      $to="абввгдезшйклмнопрстуфхцьыАБВВГДЕЗИЙКЛМНОПРСТУФХЦЬЫ";

    $from=array('h','H','w','W','a','b','v','g','d','e','yo','zh','z','i','k','l','m','n','o','p','r','s','t','u','f','kh','ts','c','ch','sh','sch','\'','\'','e','yu','ya','A','B','V','G','D','E','YO','ZH','Z','I','K','L','M','N','O','P','R','S','T','U','F','KH','TS','C','CH','SH','SCH','\'','\'','E','YU','YA');
    $to=array('х','Х','в','В','а','б','в','г','д','е','ё','ж','з','и','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ц','ч','ш','щ','ъ','ь','э','ю','я','А','Б','В','Г','Д','Е','Ё','Ж','З','И','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ц','Ч','Ш','Щ','Ъ','Ь','Э','Ю','Я');

    $s =  str_replace($from,$to,$s);

	$s = preg_replace("!([бвгджзклмнпрстфхцчшщ])j\b!u", "$1ь", $s);
	$s = preg_replace("!([БВГДЖЗКЛМНПРСТФХЦЧШЩ])J\b!u", "$1Ь", $s);
	$s = preg_replace("!([а-я])4\b!u", "$1ч", $s);
	$s = preg_replace("!([А-Я])4\b!u", "$1Ч", $s);
	$s = preg_replace("!([а-я])4([а-я])!u", "$1ч$2", $s);
	$s = preg_replace("!([А-Я])4([А-Я])!u", "$1Ч$2", $s);

	$s = str_replace("4то", "что", $s);
	$s = str_replace("4ТО", "ЧТО", $s);

	$s = preg_replace("!\By\B!u", "ы", $s);
	$s = preg_replace("!\BY\B!u", "Ы", $s);
	$s = preg_replace("!([аеиоуыэюя])y\b!u", "$1й", $s);
	$s = preg_replace("!([АЕИОУЫЭЮЯ])Y\b!u", "$1Й", $s);

	$s = str_replace(array('j','J','y','Y'), array('й', 'Й','ы','Ы'), $s);

    return $s;
}

    function to_translit($s)
    {   
        $from=array('а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я','А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я');
        $to=array('a','b','v','g','d','e','yo','zh','z','i','j','k','l','m','n','o','p','r','s','t','u','f','kh','ts','ch','sh','sch','\'','y','\'','e','yu','ya','A','B','V','G','D','E','YO','ZH','Z','I','J','K','L','M','N','O','P','R','S','T','U','F','KH','TS','CH','SH','SCH','\'','Y','\'','E','YU','YA');
        return str_replace($from,$to,$s);
    }
?>
