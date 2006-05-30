<?
    function lcml_lists($txt)
    {
        $txt = split("\n",$txt);
        $sum = array();
        $ul_open=0;
        $res = '';
        foreach($txt as $s)
        {
        	$m=array();
            if(preg_match("!^([\*]+) !m", $s, $m))
            {
                $len = strlen($m[1]);
                if($ul_open<$len)
                    for($ul_open;$ul_open<$len;$ul_open++)
                        $res.="<ul>";
                if($ul_open>$len)
                    for($ul_open;$ul_open>$len;$ul_open--)
                        $res.="</ul>";
                $s = @preg_replace("!^\*+\s+(.+)$!e","'<li>'.lcml(stripslashes(\"$1\")).'</li>'\n",$s);
                $res .= $s;
            }
            else
            {
                if($res)
                {
                    for($ul_open;$ul_open>0;$ul_open--)
                        $res.="</ul>";
                    $sum[] = $res;
                    $res = '';
                }
                $sum[] = $s;
            }
        }
        if($res)
            $sum[] = $res;

        return join("\n",$sum);
    }
?>
