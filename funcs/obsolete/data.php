<?
    require_once("funcs/obsolete/hts.php");
    require_once("debug.php");

    $GLOBALS['transferred_fields']='body source title description forum_id type cr_type split_type template create_time copyright style color logdir flags nav_name';

    function hts_get($page_uri)
    {
//        $GLOBALS['log_level']=2;

        echolog("Try load hts_data for '$page_uri'",4);
    
        _obsolete_hts_load($page_uri);


        $GLOBALS['title'] = $GLOBALS['h1'];
    
        if(!empty($GLOBALS['h3']))
	        $GLOBALS['description'] = "[b]".$GLOBALS['h3']."[/b]";
        
        if(!empty($GLOBALS['h2']))
        {
        	if(!empty($GLOBALS['description']))
        		$GLOBALS['description'] .= ", ";

			$GLOBALS['description'] .= $GLOBALS['h3'];
	 	}
        	

//        echo "********** {$GLOBALS['h1']} ***********<br>";

        $page_uri = hts_path_to_uri($page_uri);

        $hts = new DataBaseHTS();

        $names = split(' ',$GLOBALS['transferred_fields']);
        foreach($names as $var)
        {
            $tmp = $hts->get_data($page_uri,$var);
            if($tmp)
            {
                $GLOBALS[$var]=$tmp;
                echolog("Set global '$var'='$tmp' in hts_get",4);
            }
        }

        $parents=$hts->get_data_array($page_uri,'parent');

        for($i=0; $i < sizeof($parents); $i++)
            $parents[$i]=$hts->page_uri_by_id($parents[$i]);

        $parents=$GLOBALS['parents'] = join("\n",$parents);

        $names = split(' ','body head title h1 h2 h3 source copyright type forum_id create_time style template color logdir navs forum flags cr_type split_type nav_name parents');
        $data = array();
        foreach($names as $var)
            $data[$var]=isset($GLOBALS[$var])?$GLOBALS[$var]:NULL;

        return $data;
    }

    function hts_store($page_uri,$data)
    {
        $page_uri=hts_path_to_uri($page_uri);

        $hts = new DataBaseHTS();

        $names = split(' ',$GLOBALS['transferred_fields']);
        foreach($names as $var)
        {
//            echolog("Set global '$var'='{$data[$var]}' in hts_store",4);

#            $cvt_data=iconv('UTF-8','WINDOWS-1251',iconv('WINDOWS-1251','UTF-8',$data[$var]));
#            if($cvt_data!=$data[$var])
#                die("Can't convert to UTF-8: '$cvt_data'");

            $hts->set_data($page_uri,$var,@$data[$var]);
            $data[$var]='';
        }

        $hts->set_data($page_uri,'compile_time',time());

        $parents=split("\n",str_replace("\r","",$data['parents']));
        foreach($parents as $parent)
        {
            if(trim($parent))
            {
                $parent=$hts->normalize_uri($parent);
                $hts->append_data($parent,'child',$page_uri);
                $hts->append_data($page_uri,'parent',$parent);
            }
        }

//        $GLOBALS['parents'] = join("\n",$hts->get_data_array($page_uri,'parent');

        foreach($data as $key => $val)
            if($val)
                $GLOBALS[$key]=$val;
            else
                unset($GLOBALS[$key]);

        _obsolete_hts_save($page_uri);
        if(!empty($GLOBALS['obsolete_hts_modify_time']))
            $hts->set_data($page_uri,'modify_time', $GLOBALS['obsolete_hts_modify_time']);
    }

    function hts_path_to_uri($page_uri)
    {   
//        echo "Convert uri from '$page_uri' to ";
        $page_uri=preg_replace("!^{$GLOBALS['doc_root']}/!","/",$page_uri);
        if($page_uri[0]=='/')
            $page_uri="http://".$GLOBALS['host'].$page_uri;
//        echo "'$page_uri'<br>";
        $page_uri=preg_replace("!\.(hts)!",".php",$page_uri);
        return $page_uri;
    }

?>
