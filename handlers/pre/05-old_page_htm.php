<?
    register_handler('!^(http://[^/]+.*/.*)(\-t)?\.htm$!', 'handler_old_page_htm');

    function handler_old_page_htm($uri, $m=array())
	{
	    //Если ссылка на старую страницу (*.htm или *-t.htm) и есть *.phtml или */index.phtml - переход на index.phtml

        $hts = new DataBaseHTS;

		if($hts->get_data($m[1].'.phtml', 'source'))
		{
            go($m[1].'.phtml');
			return true;
		}

		if($hts->get_data($m[1].'.php', 'source'))
		{
            go($m[1],'.php');
			return true;
		}

		if($hts->get_data($m[1].'/', 'source'))
		{
            go($m[1].'/');
			return true;
		}
		
		return false;
    }
?>
