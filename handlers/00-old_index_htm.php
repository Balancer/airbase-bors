<?
    register_uri_handler('!^(http://[^/]+.*/)index(\-t)?\.htm$!', 'handler_old_index_htm');

    function handler_old_index_htm($uri, $m=array())
	{
	    //Если ссылка на старую страницу (index.htm или index-t.htm) и есть index.phtml - переход на index.phtml

        $hts = new DataBaseHTS;
		if($hts->get_data($m[1], 'source'))
		{
            go($m[1]);
			return true;
		}
		
		return false;
    }
?>
