<?            
    require_once('funcs/DataBaseHTS.php');
    require_once('funcs/lcml/funcs.php');

    $log_level = 2;

    function lcml_autolinks($hts)
    {
        if(!class_exists('DataBaseHTS'))
            return $hts;

        $h = new DataBaseHTS();

        $map=links_map($hts);

        foreach($h->dbh->get_array("SELECT * FROM `hts_data_autolinks` ORDER BY `value` DESC;") as $i)
        {
            $key = $i['value'];

            if($key && strpos($hts,$key,0) !== false)
            {
                $pos=-1;
                do
                {
                    $pos=strpos($hts,$key,$pos+1);

                    if($pos!==false && substr($map,$pos,1) == '.' && c_type(substr($hts,$pos-1,1))!=c_type(substr($hts,$pos,1)) && c_type(substr($hts,$pos+strlen($key)-1,1)) != c_type(substr($hts,$pos+strlen($key),1)))
                    {
                        $link = $i['id'];
                        $ins="<a href=\"$link\" class=\"autolink\">$key</a>";
                        $hts=substr($hts,0,$pos).$ins.substr($hts,$pos+strlen($key));
                        $map=substr($map,0,$pos).str_repeat('x',strlen($ins)).substr($map,$pos+strlen($key));
                        $pos+=strlen($ins)-1;
                        do
                        {
                            $pos=strpos($hts,$key,$pos+1);
                            if($pos!==false)
                            {
                                $map=substr($map,0,$pos).str_repeat('x',strlen($key)).substr($map,$pos+strlen($key));
                            }
                        } while($pos!==false && $pos<strlen($hts)-strlen($key));
                        $pos=-1;
                    }
                }while($pos!==false && $pos<strlen($hts)-strlen($key));
            }
        }
    return $hts;
    }

    function links_map($txt)
    {
        $map='';
        $in_tag=0;
        $in_script=0;
        $in_href=0;
        $length = strlen($txt);
        for($i=0; $i<$length; $i++)
        {
            $r=substr($txt,$i,6);
            $c=substr($r,0,1);
            if($c == '<')
            {
                if(preg_match("/^<scrip/iu",$r)) $in_script=1;
                if(preg_match("/^<\?/u",$r)) $in_script=1;
                if(preg_match("/^<\/scri/iu",$r)) $in_script=0;
                if(preg_match("/^<a /iu",$r)) $in_href=1;
                if(preg_match("/^<\/a>/iu",$r)) $in_href=0;
                if($c == '<' && !$in_script && !$in_href) $in_tag=1;
            }
            else
            {
                if($c == '?')
                {
                    if(preg_match("/^\?>/",$r)) 
                        $in_script=0;
                }
                else
                {
                    $in_tag = $in_tag && ($c != '>' || $in_script || $in_href);
                }
            }
            $map.=($in_script or $in_tag or $in_href)?"x":".";
        }
        return $map;
    }
?>
