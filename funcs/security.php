<?
function secure_path($path)
{
    $path=preg_replace("~//~","/",$path);
    $path=preg_replace("~/([^/]+?)/\.\.~","",$path);
    $path=preg_replace("~/\.\.~","",$path);
    return $path;
}

function get_script($s="")
{
    global $script;

    $script=$s?$s:$script;

    if(!$script)
//        if($QUERY_STRING)
//            $script=$QUERY_STRING;
//        else
            $script=$SCRIPT_NAME;

    if($script)
    {
        if($script[0]!="/")
            $script="/$script";
        if(!preg_match("!^.*/\w+?\.\w+?$!",$script))
        {
            if(substr($script,-1)!="/")
                $script="$script/";
            $script=$script."index.hts";
        }
        $script=preg_replace("!^(.*/)(\w+?)\.\w+?$!","\\1\\2.hts",$script);
        $script=secure_path(preg_replace("!^{$_SERVER['DOCUMENT_ROOT']}!","",$script));
    }

    return $script;
}

function show_access($level)
{
    $user_level=user_data("level");
    $user_nick=user_data("nick");

    if(!$user_nick)
    {
        echo "[Вы не <a href=/users>зарегистрированы</a>. <font color=red>Доступ запрещён</font>.]";
        return;
    }

    $color=$user_level>=$level?"green":"red";
    echo "[<a href=/users/><b>$level/$user_level</b></a> - ";
    echo "<font color=$color>доступ ";
    echo $user_level>=$level?"разрешён":"запрещён";
    echo "</font>]";
}

?>
