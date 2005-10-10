<?            
    require_once('funcs/DataBaseHTS.php');
    require_once('funcs/lcml/funcs.php');

    function lcml_keywords($txt)
    {
        if(!class_exists('DataBaseHTS'))
            return $txt;

        $h = new DataBaseHTS();

        $out = '';
        $passed_pages = array();
        $passed_ids   = array();

        $n = 0;

        foreach($h->dbh->get_array("SELECT * FROM `hts_data_keyword` ORDER BY `value` DESC;") as $i)
        {
            $key = $i['value'];
            $id  = $i['id'];

            if(!empty($passed_ids[$id]))
                continue;

            $passed_ids[$id] = 1;

            if(@$key)
            {
                $pos = -1;
                while(($pos=strpos($txt,$key,$pos+1)) !== false)
                {
                    if(c_type(substr($txt,$pos-1,1))!=c_type(substr($txt,$pos,1)) && c_type(substr($txt,$pos+strlen($key)-1,1)) != c_type(substr($txt,$pos+strlen($key),1)))
                    {
                        if($id)
                        {
                            $title = $h->get_data($id, 'title', @$link);
                            if(empty($passed_pages[$title]))
                                $out  .= "<li><a href=\"".@$link."\">$title</a>\n";
                            $passed_pages[$title] = 1;
                            $pos = strlen($txt);
                            $n++;
                            if(0 && $GLOBALS['page'])
                            {
	                            $h->set_data($GLOBALS['page'],'rcolumn',"$link $title",
    	                        	array(
										'index' => $n,
										'last_modify' => time(),
										'type' => 'keyword'
                    	        	));
                    	 	}
                        }
                    }
                }
            }
        }

        if($out)
            $GLOBALS['cms_right_column'][] = "<dl class=\"box\"><dt>См. также</dt><dd><ul>\n$out</ul></dd></dl>\n";
        
        return $txt;
    }
?>
