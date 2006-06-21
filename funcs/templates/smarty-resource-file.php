<?
    // from PHP script
    // put these function somewhere in your application

    function smarty_resource_file_get_template($tpl_name, &$tpl_source, &$smarty)
    {
        // do database call here to fetch your template,
        // populating $tpl_source
		if(file_exists($tpl_name))
		{
			$tpl_source = ec(file_get_contents($tpl_name));
			return true;
		}

		if(file_exists($smarty->template_dir."/".$tpl_name))
		{
			$tpl_source = ec(file_get_contents($smarty->template_dir."/".$tpl_name));
			return true;
		}

        return false;
    }
    
    function smarty_resource_file_get_timestamp($tpl_name, &$tpl_timestamp, &$smarty)
    {
		if(!empty($GLOBALS['cms']['templates_cache_disabled']))
		{
			$tpl_timestamp = time();
			return true;
		}

		if(file_exists($tpl_name))
		{
			$tpl_timestamp = filemtime($tpl_name);
			return true;
		}

		if(file_exists($smarty->template_dir."/".$tpl_name))
		{
			$tpl_timestamp = filemtime($smarty->template_dir."/".$tpl_name);
			return true;
		}

        return false;
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
