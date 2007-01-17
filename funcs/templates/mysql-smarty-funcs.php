<?
    require_once("funcs/DataBaseHTS.php");

    // from PHP script
    // put these function somewhere in your application
    function db_get_template($tpl_name, &$tpl_source, &$smarty_obj)
    {
        // do database call here to fetch your template,
        // populating $tpl_source
        $hts = &new DataBaseHTS;
		debug($tpl_name);
//        $tpl = $hts->get_data($tpl_name, 'title');
//		if(!$tpl)
        $tpl = $hts->get_data($tpl_name, 'source');

        if($tpl) 
        {
            $tpl_source = $tpl;
            return true;
        } 
        else 
        {
            return false;
        }
    }
    
    function db_get_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj)
    {
		if(!empty($GLOBALS['cms']['templates_cache_disabled']))
			return time();
	
        global $page;
        // do database call here to populate $tpl_timestamp.
        $hts  = &new DataBaseHTS;
		debug($tpl_name);
        $time = $hts->get_data($tpl_name, 'modify_time');

        if($page)
            $time = max($time, $hts->get_data($page, 'modify_time'), $hts->get_data($page, 'compile_time'));

        $time = max($time, $hts->dbh->get_value('hts_ext_system_data', 'key', 'global_recompile', 'value'));

        if($time) 
        {
            $tpl_timestamp = $time;
            return true;
        } 
        else 
        {
            return false;
        }
    }

    function db_get_secure($tpl_name, &$smarty_obj)
    {
        // assume all templates are secure
        return true;
    }

    function db_get_trusted($tpl_name, &$smarty_obj)
    {
        // not used for templates
    }

    // register the resource name "db"
