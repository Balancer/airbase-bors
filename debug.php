<?
    function echolog($message,$level=3)
    {
        if(!isset($GLOBALS['log_level']))
            $GLOBALS['log_level']=0;

        if(!$GLOBALS['log_level'])
            return;

        if($GLOBALS['log_level']>=$level)
        {
            if(!empty($GLOBALS['echofile']))
            {
                $fh=fopen($GLOBALS['echofile'],"at");
                fputs($fh,"$level: $message\n-------------------------------\n");
                fclose($fh);
            }
            else
            {
                if($level<3) echo '<font color="red">';
                echo "<font size=\"1\">".substr($message,0,512)."</font><br>";
                if($level<3) echo "</font>\n";
            }
            if($level==1)
            {
                echo "Backtrace error:<br>";
                echo DBG_GetBacktrace();
            }
            if(empty($GLOBALS['echofile']))
                echo "<hr>";
        }
    }

    function debug($message,$comment='',$level=3)
    {
//        return;
        $fh=@fopen('/home/airbase/html/logs/debug.txt','at');
        @fwrite($fh,strftime("%Y-%m-%d %H:%M:%S|").($comment?"$comment:\n":__FILE__.':'.__LINE__."\n")."$message\n==============\n");
        @fclose($fh);
    }

    function DBG_GetBacktrace()
    {
        $s = '';
        $MAXSTRLEN = 64;
   
        $s = '<pre align=left>';
        $traceArr = debug_backtrace();
        array_shift($traceArr);
        $tabs = sizeof($traceArr)-1;
        foreach($traceArr as $arr)
        {
            for ($i=0; $i < $tabs; $i++) $s .= ' &nbsp; ';
            $tabs -= 1;
            $s .= '<font face="Courier New,Courier">';
            if (isset($arr['class'])) $s .= $arr['class'].'.';
            $args = array();
            if(!empty($arr['args'])) foreach($arr['args'] as $v)
            {
                if (is_null($v)) $args[] = 'null';
                else if (is_array($v)) $args[] = 'Array['.sizeof($v).']';
                else if (is_object($v)) $args[] = 'Object:'.get_class($v);
                else if (is_bool($v)) $args[] = $v ? 'true' : 'false';
                else
                { 
                    $v = (string) @$v;
                    $str = htmlspecialchars(substr($v,0,$MAXSTRLEN));
                    if (strlen($v) > $MAXSTRLEN) $str .= '...';
                    $args[] = "\"".$str."\"";
                }
            }
            $s .= $arr['function'].'('.implode(', ',$args).')</font>';
            $Line = (isset($arr['line'])? $arr['line'] : "unknown");
            $File = (isset($arr['file'])? $arr['file'] : "unknown");
            $s .= sprintf("<font color=#808080 size=-1> # line %4d, file: <a href=\"file:/%s\">%s</a></font>",
            $Line, $File, $File);
            $s .= "\n";
        }    
        $s .= '</pre>';
        return $s;
    }

    function debug_page_stat()
    {
?>
<noindex>
Новых mysql-соединений:<?echo $GLOBALS['global_db_new_connections'];?><br />
Продолженных mysql-соединений:<?echo $GLOBALS['global_db_resume_connections'];?><br />
Всего запросов <?echo $GLOBALS['global_db_queries'];?><br />
Попадений в кеш данных: <?echo $GLOBALS['global_key_count_hit'];?><br />
Промахов в кеш данных: <?echo $GLOBALS['global_key_count_miss'];?><br />
Время генерации страницы: <?
list($usec, $sec) = explode(" ",microtime());
echo ((float)$usec + (float)$sec) - $GLOBALS['cms']['start_microtime'];
?> сек.<br />
<?
	if($GLOBALS['cms']['cache_copy'])
		echo "Кешированная версия от ".strftime("%Y-%d-%m %H:%I", $GLOBALS['cms']['cache_copy']);
	else
		echo "Перекомпилированная версия";
?>
<br />
</noindex>
<?
    }
?>
