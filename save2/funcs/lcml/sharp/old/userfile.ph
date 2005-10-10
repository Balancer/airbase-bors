<?
    require_once('funcs/users.php');
    
    function lst_userfile($txt) 
    { 

        //#userfile 981286653|Leroy|http://airbase.uka.ru/users/Leroy/files/fly_by_from_hell.jpg|Просто фотка для теста...
        list($time, $author, $url, $desc) = split("\|", $txt."|||");
        $nick=user_data($author, "nick", $author);

        $time=strftime("%d.%m.%Y %H:%M",$time);

        return "* $time: <a href=\"$url\">".basename($url)."</a> - $desc /$nick/\n";
    }
?>