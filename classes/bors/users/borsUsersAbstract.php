<?
	require_once('classes/bors/BorsBasePage.php');
	class borsUsersAbstract extends BorsBasePage
	{
		function template()
		{ 
			return "xfile://{$_SERVER['DOCUMENT_ROOT']}/cms/templates/forum/_header.html"; 
		}
		
        function cache_life_time()
        {
            $GLOBALS['cms']['cache_disabled'] = false;
			$GLOBALS['cms']['cache_static'] = true;
            return 600;
        }
	}
