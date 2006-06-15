<?
    // from PHP script
    // put these function somewhere in your application

    function smarty_resource_file_get_template($tpl_name, &$tpl_source, &$smarty_obj)
    {
        // do database call here to fetch your template,
        // populating $tpl_source
		if(file_exists($tpl_name))
		{
			$tpl_source = ec(file_get_contents($tpl_name));
			return true;
		}
        else 
        {
            return false;
        }
    }
    
    function smarty_resource_file_get_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj)
    {
		if(!empty($GLOBALS['cms']['templates_cache_disabled']))
			return time();

		if(file_exists($tpl_name))
		{
			$tpl_timestamp = filemtime($tpl_name);
			return true;
		}
        else 
        {
            return false;
        }
    }

    function smarty_resource_file_get_secure($tpl_name, &$smarty_obj)
    {
        // assume all templates are secure
        return true;
    }

    function smarty_resource_file_get_trusted($tpl_name, &$smarty_obj)
    {
        // not used for templates
    }
?>
