<?
    require_once("../filesystem_ext.php");
    require_once("../translit.php");

    function lcml_steal_image($url)
    {
        $url = urlencode ($url);
        $url = preg_replace("!^http://!","",$url);
        $url = preg_replace("!^www\.!","",$url);
        $url = to_translit($url);

        $url = preg_replace("![^\w\d\-\_\.]!",'_',$url);

        $first_char = substr($url,0,1);
        $two_chars = substr($url,0,2);
        $dir = $GLOBALS['cms_images_dir'].'/'.substr($url,0,1).'/'.substr($url,0,2)."/$url";
        mkdirs($dir);
    }

    function lcml_images($txt)
    {
        $txt=preg_replace("!\[(http://[^\s\|\]]+?)\]!ie","'<a href=\"$1\">'.lcml_urls_title('$1').'</a>'",$txt);
        $txt=preg_replace("!\[(www\.[^\s\|\]]+?)\]!ie","'<a href=\"http://$1\">'.lcml_urls_title('http://$1').'</a>'",$txt);

        $txt=preg_replace("!(?<=\s|^|\()(http://\S+)(\)|\.|,|\!|\-)(?=\s|$)!ie","'<a href=\"$1\">'.lcml_urls_title('$1').'</a>$2'",$txt);
        $txt=preg_replace("!(?<=\s|^|\()(http://\S+)(?=\s|$)!ie","'<a href=\"$1\">'.lcml_urls_title('$1').'</a>'",$txt);
        $txt=preg_replace("!(?<=\s|^|\()(www\.\S+)(\)|\.|,|\!|\-)(?=\s|$)!ie","'<a href=\"http://$1\">'.lcml_urls_title('http://$1').'</a>$2'",$txt);
        $txt=preg_replace("!(?<=\s|^|\()(www\.\S+)(?=\s|$)!ie","'<a href=\"http://$1\">'.lcml_urls_title('http://$1').'</a>'",$txt);

        return $txt;
    }
?>
