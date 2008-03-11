<?

    function include_source($uri)
    {
        $hts = new DataBaseHTS();
        return $hts->get_data($uri, $key);
   	}

	echo include_source($GLOBALS['module_data']['url']);
?>
