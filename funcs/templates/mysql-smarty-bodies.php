<?
    require_once("funcs/DataBaseHTS.php");

    // from PHP script
    // put these function somewhere in your application
    function hts_body_get_template ($tpl_name, &$tpl_source, &$smarty_obj)
    {
        // do database call here to fetch your template,
        // populating $tpl_source
        $hts = &new DataBaseHTS();
        $tpl = $hts->get_data($tpl_name, 'body');

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
    
    function hts_body_get_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj)
    {
        global $page;
        // do database call here to populate $tpl_timestamp.
        $hts  = &new DataBaseHTS();
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

    function hts_body_get_secure($tpl_name, &$smarty_obj)
    {
        // assume all templates are secure
        return true;
    }

    function hts_body_get_trusted($tpl_name, &$smarty_obj)
    {
        // not used for templates
    }

    // register the resource name "db"

    $smarty->register_resource("htsb", array("hts_body_get_template",
                                       "hts_body_get_timestamp",
                                       "hts_body_get_secure",
                                       "hts_body_get_trusted"));
